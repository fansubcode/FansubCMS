<?php

/**
 * News_Model_News
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    FansubCMS
 * @subpackage Models
 * @author     FansubCMS Dev Team <hikaru@fansubcode.org>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class News_Model_News extends News_Model_Base_News
{
    /**
     * get News
     * @param integer $limit
     * @param integer $offset
     * @param bool | null $public
     * @return Doctrine_Collection
     */
    static function getNews ($limit, $offset, $public = true)
    {
        $q = Doctrine_Query::create();
        
        $q->from('News_Model_News n')
            ->limit($limit)
            ->offset($offset);
        
        if ($public === false) {
            $q->where('public = ?', 'no');
        } else if ($public === true) {
            $q->where('public = ?', 'yes')
                ->andWhere('publish_date <= NOW()');
        }
        
        return $q->execute();
    }
    /**
     * get one news by its id
     * @param integer $id
     * @return News
     */
    static function getNewsById ($id)
    {
        $q = Doctrine_Query::create();
        $q->from('News_Model_News n')->where('id = ?', $id);
        return $q->fetchOne();
    }
    /**
     * get news for permalinks
     * @param integer $day
     * @param integer $month
     * @param integer $year
     * @param string $title
     * @return string
     */
    static function getNewsBySlug($slug)
    {
        $q = Doctrine_Query::create();
        $q->from('News_Model_News n')
            ->where('title_slug = ?', $slug);
        return $q->fetchOne();
    }
    /**
     * retrieve url parameters for permalinks or not
     * @return string
     */
    public function getUrlParams ()
    {
        $conf = Zend_Registry::get('environmentSettings');
        if ($conf->news->usePermaLink) {
            return 'article/' . urlencode($this->title_slug);
        } else {
            return 'comment/index/id/' . $this->id;
        }
    }
    /**
     * gets comments that are not marked as spam or invisible
     * @return Doctrine_Collection
     */
    public function getComments ()
    {
        $q = Doctrine_Query::create();
        $q->from('News_Model_Comment nc')
            ->where('spam = ?', 'no')
            ->andWhere('visible = ?', 'yes')
            ->andWhere('news_id = ?', $this->id)
            ->orderBy('nc.created_at DESC');
        ;
        return $q->execute();
    }
    /**
     * returns a Zend_Paginator_Object
     * @see Zend_Paginator
     * @param mixed $spam true spam false non-spam everything else all
     * @param mixed $visible true visible false invisible everything else all
     * @return Zend_Paginator
     */
    public function getCommentPaginator ($spam = null, $visible = null)
    {
        $q = Doctrine_Query::create();
        $q->from('News_Model_Comment nc')->orderBy('nc.created_at DESC');
        if ($spam === true) {
            $q->andWhere('nc.spam = ?', 'yes');
        } else 
            if ($spam === false) {
                $q->andWhere('nc.spam = ?', 'no');
            }
        if ($visible === true) {
            $q->andWhere('nc.visible = ?', 'yes');
        } else 
            if ($visible === false) {
                $q->andWhere('nc.visible = ?', 'no');
            }
        $q->andWhere('nc.news_id = ?', $this->id);
        $adapter = new FansubCMS_Paginator_Adapter_Doctrine($q);
        return new Zend_Paginator($adapter);
    }
}