<?php
/**
 * AppBaseController.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Application\Controller;


use Zend\Mvc\Controller\AbstractActionController;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\View\Helper\AbstractHelper;
use Zend\View\HelperPluginManager;


/**
 * Class AppBaseController
 * @package Application\Controller
 *
 * @method \Application\Controller\Plugin\AppLoggerPlugin appLogger()
 * @method \Application\Controller\Plugin\AppConfigPlugin appConfig()
 * @method \Application\Controller\Plugin\AppServerPlugin appServer()
 * @method \Application\Controller\Plugin\AppAsyncPlugin appAsync()
 */
class AppBaseController extends AbstractActionController
{
    const RESPONSE_JSON = 'application/json';
    const RESPONSE_HTML = 'text/html';
    const RESPONSE_PLAIN = 'text/plain';

    /**
     * @var array
     */
    private $resultData;

    public function __construct()
    {
        $this->setResultData();
    }


    /**
     * @param null $helper
     * @return AbstractHelper|HelperPluginManager
     */
    protected function appViewHelperManager($helper = null)
    {
        $helperManager = $this->appServiceManager('ViewHelperManager');
        if (null === $helper) {
            return $helperManager;
        }
        return $helperManager->get($helper);
    }


    /**
     * @param null $service
     * @return ServiceLocatorInterface|ServiceManager|mixed
     */
    protected function appServiceManager($service = null)
    {
        $manager = $this->getEvent()->getApplication()->getServiceManager();
        if (null === $service) {
            return $manager;
        }
        return $manager->get($service);
    }


    /**
     * Add data to result object
     *
     * @param string $key
     * @param null|string|array|\stdClass $data
     */
    protected function addResultData($key, $data = null)
    {
        $this->resultData['data']->{$key} = $data;
    }


    /**
     * Init the result object
     *
     * @param array $resultData
     */
    protected function setResultData($resultData = [])
    {
        if (!isset($resultData['code'])) {
            $resultData['code'] = 0;
        }
        if (!isset($resultData['message'])) {
            $resultData['message'] = 'Success';
        }
        if (!isset($resultData['type'])) {
            $resultData['type'] = self::RESPONSE_HTML;
        }

        if (!isset($resultData['data'])) {
            $resultData['data'] = new \stdClass();
        } else {
            if (!$resultData instanceof \stdClass) {
                $resultData['data'] = new \stdClass();
            }
        }
        $this->resultData = $resultData;
    }


    /**
     * @param string $type
     */
    protected function setResultType($type)
    {
        $this->resultData['type'] = $type;
    }

    /**
     * @param string $text
     */
    protected function setResultTextData($text)
    {
        $this->setResultType(self::RESPONSE_PLAIN);
        $this->resultData['content'] = $text;
    }

    /**
     * @param int $code
     * @param string $message
     */
    protected function setResultCodeMessage($code = 0, $message = 'Success')
    {
        $data = $this->getResultData();
        $data['code'] = $code;
        $data['message'] = $message;
        $this->setResultData($data);
    }

    /**
     * @return array
     */
    public function getResultData()
    {
        return $this->resultData;
    }
}