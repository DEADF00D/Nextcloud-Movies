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
use OCP\AppFramework\Http\TemplateResponse;

class WatchController extends Controller {
    /** @var string */
	private $userId;
	/** @var IRootFolder */
	private $rootFolder;

	public function __construct(string $appName, IRequest $request, string $userId, IRootFolder $rootFolder, IServerContainer $serverContainer) {
		parent::__construct($appName, $request);
		$this->userId = $userId;
        $this->rootFolder = $rootFolder;
        $this->serverContainer = $serverContainer;
	}

    /**
	 * @NoAdminRequired
     * @NoCSRFRequired
	 */
	public function nowWatch(string $id = '') {
        $userFolder=$this->rootFolder->getUserFolder($this->userId);
        $file=$userFolder->getById(intval($id));
        $path=$userFolder->getRelativePath($file[0]->getPath());

        return new TemplateResponse('movies', 'watch', [
            'filename' => $path,
            'userId' => $this->userId,
            'mimetype' => $file[0]->getMimeType()
        ]);
	}
}
