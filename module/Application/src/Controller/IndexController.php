<?php
/**
 * IndexController.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Application\Controller;



class IndexController extends AppBaseController
{

    public function indexAction()
    {
        $this->addResultData('name', 'Baby!');
    }

}