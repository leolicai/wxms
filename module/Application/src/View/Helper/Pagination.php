<?php
/**
 * Pagination.php
 *
 * Author: leo <camworkster@gmail.com>
 * Date: 2017/8/31
 * Version: 1.0
 */

namespace Application\View\Helper;


use Zend\View\Helper\AbstractHelper;


class Pagination extends AbstractHelper
{

    /**
     * @var integer
     */
    private $count;

    /**
     * @var integer
     */
    private $size;

    /**
     * @var string
     */
    private $urlTpl;

    /**
     * @var integer
     */
    private $page;


    public function __construct()
    {
        $this->count = 0;
        $this->page = 1;
        $this->size = 0;
        $this->urlTpl = '';
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param int $count
     * @return Pagination
     */
    public function setCount($count)
    {
        $this->count = $count;
        return $this;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param int $size
     * @return Pagination
     */
    public function setSize($size)
    {
        $this->size = $size;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrlTpl()
    {
        return $this->urlTpl;
    }

    /**
     * @param string $urlTpl
     * @return Pagination
     */
    public function setUrlTpl($urlTpl)
    {
        $this->urlTpl = urldecode($urlTpl);
        return $this;
    }

    /**
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param int $page
     * @return Pagination
     */
    public function setPage($page)
    {
        $this->page = $page;
        return $this;
    }

    /**
     * @return string
     */
    public function render()
    {
        $pages = ceil($this->count / $this->size);
        if ($pages <= 1 || $this->page > $pages) {
            return '';
        }

        $html = '<div class="row">';
        $html .= '<div class="col-sm-6">';

        $html .= '<ul class="pagination"><li>';
        $html .= '当前第 <strong>' . $this->page . '</strong>页, 总计 <strong>' . $this->count . '</strong> 条';
        $html .= '</li></ul>';

        $html .= '</div>';
        $html .= '<div class="col-sm-6 text-right">';
        $html .= '<ul class="pagination">';


        if (1 == $this->page) {
            $html .= '<li class="disabled"><span>首页</span></li>';
            $html .= '<li class="disabled"><span>&lt;</span></li>';
        } else {
            $html .= '<li><a href="' . sprintf($this->urlTpl, 1) . '">首页</a></li>';
            $html .= '<li><a href="' . sprintf($this->urlTpl, ($this->page - 1)) . '">&lt;</a></li>';
        }

        $showLinksCount = 7;
        if ($showLinksCount >= $pages) {
            $start = 1;
            $end = $pages;
        } else {
            $sideLength = intval($showLinksCount / 2);

            $start = $this->page - $sideLength;
            $end = $this->page + $sideLength;
            if ($start <= 0) {
                $end += (1 - $start);
                $start = 1;
            }
            if ($end > $pages) {
                $start -= ($end - $pages);
                $end = $pages;
            }
        }


        for ($i = $start; $i <= $end; $i++) {
            if ($i == $this->page) {
                $html .= '<li class="active"><a href="' . sprintf($this->urlTpl, $i) . '">' . $i . '</a></li>';
            } else {
                $html .= '<li><a href="' . sprintf($this->urlTpl, $i) . '">' . $i . '</a></li>';
            }
        }

        if ($pages == $this->page) {
            $html .= '<li class="disabled"><span>&gt;</span></li>';
            $html .= '<li class="disabled"><span>末页</span></li>';
        } else {
            $html .= '<li><a href="' . sprintf($this->urlTpl, ($this->page + 1)) . '">&gt;</a></li>';
            $html .= '<li><a href="' . sprintf($this->urlTpl, $pages) . '">末页</a></li>';
        }


        $html .= '</ul>';
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

}