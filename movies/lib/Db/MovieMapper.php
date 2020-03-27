<?php
namespace OCA\Movies\Db;

use OCP\IDbConnection;
use OCP\AppFramework\Db\QBMapper;

class MovieMapper extends QBMapper {

    public function __construct(IDbConnection $db) {
        parent::__construct($db, 'movies_cache', Movie::class);
    }

    public function find(string $filename) {
        $qb = $this->db->getQueryBuilder();

        $qb->select('*')
            ->from($this->getTableName())
            ->where(
                $qb->expr()->eq('filename', $qb->createNamedParameter($filename))
            );

        return $this->findEntity($qb);
    }
}
