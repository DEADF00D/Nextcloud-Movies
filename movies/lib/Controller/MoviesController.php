<?php
namespace OCA\Movies\Controller;

use OCA\Movies\AppInfo\Application;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\JSONResponse;

use OCP\Files\File;
use OCP\Files\Folder;
use OCP\Files\IRootFolder;
use OCP\Files\Node;
use OCP\Files\NotFoundException;
use OCP\Files\NotPermittedException;
use OCP\Files\StorageNotAvailableException;

use OCP\IRequest;
use OCP\IServerContainer;

use OCA\Movies\Db\Movie;
use OCA\Movies\Db\MovieMapper;
use OCA\Movies\Storage\MovieArtStorage;

use OCA\Movies\TMDb\TMDb;
use OCA\Movies\PTN\PTN;

class MoviesController extends Controller {

	/** @var string */
	private $userId;
	/** @var IRootFolder */
	private $rootFolder;

	public function __construct(string $appName, IRequest $request, string $userId, MovieMapper $mapper, IRootFolder $rootFolder, IServerContainer $serverContainer) {
		parent::__construct($appName, $request);
		$this->userId = $userId;
		$this->mapper = $mapper;
        $this->rootFolder = $rootFolder;
        $this->serverContainer = $serverContainer;

		$this->initCache();
	}

	/**
	 * @NoAdminRequired
     * @NoCSRFRequired
	 */
	public function myMovies(string $path = ''): JSONResponse {
		return $this->generate($path, false);
	}

    /**
	 * @NoAdminRequired
     * @NoCSRFRequired
	 */
	public function myMovie(string $path): JSONResponse {
		$userFolder = $this->rootFolder->getUserFolder($this->userId);

        try{
            $file=$userFolder->get($path);
        }catch(NotFoundException $e){
            return new JSONResponse([
                'status'=>false,
                'msg'=>'Not found'
            ], Http::STATUS_OK);
        }

        if(!in_array($file->getMimeType(), Application::MIMES)){
            return new JSONResponse([
                'status'=>false,
                'msg'=>'Invalid file type.'
            ], Http::STATUS_OK);
        }

        return new JSONResponse($this->processMovie($file), Http::STATUS_OK);
	}

	public function initCache(){
		$userFolder = $this->rootFolder->getUserFolder($this->userId);
        try{
            $userFolder->get("/.movies-cache");
        }catch(NotFoundException $e){
            try{
                $userFolder->newFolder('/.movies-cache');
            }catch(NotPermittedException $e){}
        }
	}

	public function buildCache($filename){
		$userFolder = $this->rootFolder->getUserFolder($this->userId);

		$ptn=new PTN();
		$fileinfos=$ptn->parse($filename);

		$tmdb=new TMDb();
		$searches=$tmdb->Search($fileinfos['title']);

		$tmdbinfos=$tmdb->Movie($searches[0]);

		$artpath=explode('/',$tmdbinfos['art']);
		$artfilename=$artpath[count($artpath)-1];

		$file = $userFolder->newFile('/.movies-cache/'.$artfilename);
		if(in_array('art', array_keys($tmdbinfos))){
			$artcontent=$tmdb->HTTPGet($tmdbinfos['art']);
			$file->putContent($artcontent);
		}

		$result=[
			"PTN" => $fileinfos,
			"TMDb" => $tmdbinfos,
			"LocalArt" => $file->getId()
		];

		$cache=new Movie();
		$cache->setFilename($filename);
		$cache->setInfos(json_encode($result));
		$this->mapper->insert($cache);

		return $result;
	}

	public function getCache($filename){
		try{
			$exist=$this->mapper->find($filename);
			return json_decode($exist->getInfos(), true);
		} catch(DoesNotExistException $e) {
			return false;
        }
	}

    public function processMovie($node): array{
		$userFolder = $this->rootFolder->getUserFolder($this->userId);
        $filename = $node->getName();

        $result = $this->getCache($filename);
		if($result === false){
			$result = $this->buildCache($filename);
		}

        return [
            'movie' => $result,
            'etag' => $node->getEtag(),
            'fileid' => $node->getId(),
            'etag' => $node->getEtag(),
            'lastmod' => $node->getMTime(),
            'mime' => $node->getMimetype(),
            'size' => $node->getSize(),
            'type' => $node->getType()
        ];
    }

	private function generate(string $path, bool $shared): JSONResponse {
		$userFolder = $this->rootFolder->getUserFolder($this->userId);

		$folder = $userFolder;
		if ($path !== '') {
			try {
				$folder = $userFolder->get($path);
			} catch (NotFoundException $e) {
				return new JSONResponse([], Http::STATUS_NOT_FOUND);
			}
		}

		$data = $this->scanCurrentFolder($folder, $shared);
		$result = $this->formatData($data);

		return new JSONResponse($result, Http::STATUS_OK);
	}

	private function formatData(iterable $nodes): array {
		$userFolder = $this->rootFolder->getUserFolder($this->userId);

		$result = [];
		/** @var Node $node */
		foreach ($nodes as $node) {
			// properly format full path and make sure
			// we're relative to the user home folder
			$isRoot = $node === $userFolder;
			$path = $userFolder->getRelativePath($node->getPath());

            if($node->getType()!='dir'){
                $curres=$this->processMovie($node);
                if($curres['movie']['TMDb']!=[]){
                    $result[]=$curres;
                }
            }
		}

		return $result;
	}

	private function scanCurrentFolder(Folder $folder, bool $shared): iterable  {
		$nodes = $folder->getDirectoryListing();

		// add current folder to iterable set
		yield $folder;

		foreach ($nodes as $node) {
			if ($node instanceof Folder && $this->scanFolder($node, 0, $shared)) {
				yield $node;
			} elseif ($node instanceof File) {
				if ($this->validFile($node, $shared)) {
					yield $node;
				}
			}
		}
	}

	private function validFile(File $file, bool $shared): bool {
		if (in_array($file->getMimeType(), Application::MIMES) && $this->isShared($file) === $shared) {
			return true;
		}

		return false;
	}

	private function isShared(Node $node): bool {
		return $node->getStorage()->instanceOfStorage(SharedStorage::class);
	}

	private function scanFolder(Folder $folder, int $depth, bool $shared): bool {
		if ($depth > 4) {
			return false;
		}

		try {
			// Ignore folder with a .noimage or .nomedia node
			if ($folder->nodeExists('.nomovie') || $folder->nodeExists('.nomedia')) {
				return false;
			}

			$nodes = $folder->getDirectoryListing();
		} catch (StorageNotAvailableException $e) {
			return false;
		}

		foreach ($nodes as $node) {
			if ($node instanceof File) {
				if ($this->validFile($node, $shared)) {
					return true;
				}
			}
		}

		foreach ($nodes as $node) {
			if ($node instanceof Folder && $this->isShared($node) === $shared) {
				if ($this->scanFolder($node, $depth + 1, $shared)) {
					return true;
				}
			}
		}

		return false;
	}
}
