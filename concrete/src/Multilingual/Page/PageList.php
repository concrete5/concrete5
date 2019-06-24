<?php
namespace Concrete\Core\Multilingual\Page;

use Concrete\Core\Page\PageList as CorePageList;
use Concrete\Core\Multilingual\Page\Section\Section;
use Database;
use Doctrine\DBAL\Query\QueryBuilder;

defined('C5_EXECUTE') or die('Access Denied.');

class PageList extends CorePageList
{
    protected $includeAliases = false;

    public function finalizeQuery(QueryBuilder $query)
    {
        $db = Database::connection();
        $query = parent::finalizeQuery($query);

        $mainRelation = $db->createQueryBuilder();
        $mainRelation
            ->select('mpr0.cID')->addSelect('MIN(mpr0.mpRelationID) as mpr')
            ->from('MultilingualPageRelations', 'mpr0')
            ->groupBy('mpr0.cID')
        ;
        $query
            ->addSelect('mppr.mpr')
            ->leftJoin('p', '('.$mainRelation.')', 'mppr', 'p.cID = mppr.cID');

        $mslist = Section::getList();

        foreach ($mslist as $ms) {
            $cID = (int) $ms->getCollectionID();
            $cLocale = (string) $ms->getLocale();
            $query
                ->addSelect("count(mppr$cID.mpRelationID) as relationCount$cID")
                ->leftJoin('mppr', 'MultilingualPageRelations', "mppr$cID", "mppr.mpr = mppr$cID.mpRelationID AND ".$db->quote($cLocale)." = mppr$cID.mpLocale")
            ;
        }
        $query->addGroupBy(['p.cID', 'mppr.mpr']);

        return $query;
    }

    public function filterByMissingTargets($targets)
    {
        if (!empty($targets)) {
            $db = Database::connection();
            for ($i = 0; $i < count($targets); ++$i) {
                $having = $db->createQueryBuilder();
                $t = $targets[$i];
                $this->query->having(
                    $having->expr()->orX('count(mppr'.($t->getCollectionID()).'.mpRelationID) = 0')
                );
            }
        }
    }
}
