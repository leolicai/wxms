<?php
/**
 * Module.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Application;


use Zend\Mvc\MvcEvent;


class Module
{
    const ACCEPT_TYPE_HTML = 'text/html';
    const ACCEPT_TYPE_JSON = 'application/json';
    const ACCEPT_TYPE_PLAIN = 'text/plain';



    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }


    /**
     *
     * @param MvcEvent $event
     */
    public function onBootstrap(MvcEvent $event)
    {
        // Register listener
        $sharedEventManager = $event->getApplication()->getEventManager()->getSharedManager();
        $sharedEventManager->attach(__NAMESPACE__, MvcEvent::EVENT_DISPATCH, [$this, 'onDispatchListener'], 100);
        $sharedEventManager->attach('Zend\Mvc\Application', MvcEvent::EVENT_DISPATCH, [$this, 'onGlobalDispatchListener'], 0);
    }


    /**
     * @param MvcEvent $event
     */
    public function onDispatchListener(MvcEvent $event)
    {
        //$serviceManager = $event->getApplication()->getServiceManager();
        //$logger = $serviceManager->get('AppLogger');
        //$logger->info('This is application module dispatch listener');
    }


    /**
     * Application global listener
     *
     * @param MvcEvent $event
     */
    public function onGlobalDispatchListener(MvcEvent $event)
    {
        $serviceManager = $event->getApplication()->getServiceManager();
        $logger = $serviceManager->get('AppLogger');

        $resultData = $event->getTarget()->getResultData();
        $appConfig = $serviceManager->get('ApplicationConfig');
        $resultData['env'] = isset($appConfig['application']['env']) ? $appConfig['application']['env'] : 'development';

        $_forceType = $resultData['type'];

        // clean for production
        if ('development' != $resultData['env']) {
            unset($resultData['type']);
        }

        // For some ajax request.
        if (self::ACCEPT_TYPE_JSON == $_forceType) {
            $content = json_encode($resultData, JSON_UNESCAPED_UNICODE);
            $this->setQuickResponse($event, $content);
            $logger->debug(
                __METHOD__ . PHP_EOL .
                'Force response to application/json by action.' . PHP_EOL .
                $content
            );
            return;
        }

        // For some thirty api request
        if (self::ACCEPT_TYPE_PLAIN == $_forceType) {
            $content = @(string)$resultData['content'];
            $this->setQuickResponse($event, $content);
            $logger->debug(
                __METHOD__ . PHP_EOL .
                'Force response to text/plain by action.' . PHP_EOL .
                $content
            );
            return;
        }

        $request = $event->getRequest();
        if($request instanceof \Zend\Http\Request) {
            if (!$request->getHeaders()->has('Accept')) {
                $event->setResult($event->getResponse());
                $logger->err(
                    __METHOD__ . PHP_EOL .
                    'Request header no include accept field. Disabled response the request.'
                );
                return;
            }

            $header = $request->getHeaders()->get('Accept');
            if (! $header instanceof \Zend\Http\Header\HeaderInterface) {
                $event->setResult($event->getResponse());
                $logger->err(__METHOD__ . PHP_EOL . 'Invalid request accept header. Disabled response the request.');
                return;
            }

            $accept = \Zend\Http\Header\Accept::fromString($header->toString());
            $acceptFieldValue = $accept->getFieldValue();
            $logger->debug(
                __METHOD__ . PHP_EOL .
                'The request accept header:' . PHP_EOL .
                $accept->toString() . PHP_EOL .
                'FieldValue:' . PHP_EOL .
                $acceptFieldValue
            );

            //if ($accept->hasMediaType(self::ACCEPT_TYPE_JSON)) {
            if (self::ACCEPT_TYPE_JSON == $this->getAcceptFirstType($acceptFieldValue)) {
                $content = json_encode($resultData, JSON_UNESCAPED_UNICODE);
                $this->setQuickResponse($event, $content);
                $logger->debug(
                    __METHOD__ . PHP_EOL .
                    'The request accept type is JSON, use ' . self::ACCEPT_TYPE_JSON . ' response.' . PHP_EOL .
                    $content
                );
                return ;
            }

            //if ($accept->hasMediaType(self::ACCEPT_TYPE_HTML) || $accept->hasMediaType(self::ACCEPT_TYPE_PLAIN)) {
            if (self::ACCEPT_TYPE_HTML == $this->getAcceptFirstType($acceptFieldValue) ||
                self::ACCEPT_TYPE_PLAIN == $this->getAcceptFirstType($acceptFieldValue)) {
                if ($request->isXmlHttpRequest()) {
                    $result = $event->getResult();
                    if (!$result instanceof \Zend\View\Model\ViewModel) {
                        $result = new \Zend\View\Model\ViewModel();
                    }

                    $result->setTerminal(true);
                    $result->setVariables($resultData);

                    $event->setViewModel($result);
                    $event->setResult($result);
                    $logger->debug(
                        __METHOD__ . PHP_EOL .
                        'The request is AJAX call, disabled layout.'
                    );
                } else {
                    $model = $event->getViewModel();
                    $model->setVariables($resultData);
                    if ($model->hasChildren()) {
                        foreach($model->getChildren() as $child) {
                            if ($child instanceof \Zend\View\Model\ViewModel) {
                                $child->setVariables($resultData);
                            }
                        }
                    }
                }
                return;
            }

            return;
        }

        return;
    }


    /**
     * @param string $value
     * @return string
     */
    private function getAcceptFirstType($value)
    {
        $values = explode(',', $value);
        return trim(array_shift($values));
    }


    /**
     * @param MvcEvent $event
     * @param string $content
     */
    private function setQuickResponse(MvcEvent $event, $content = '')
    {
        $headerContentType = new \Zend\Http\Header\ContentType();
        $headerContentType->setMediaType(self::ACCEPT_TYPE_JSON);
        $headerContentType->setCharset('UTF-8');

        $responseHeaders = new \Zend\Http\Headers();
        $responseHeaders->addHeader($headerContentType);

        $response = $event->getResponse();
        if (! $response instanceof \Zend\Http\Response) {
            $response = new \Zend\Http\Response();
        }

        $response->setHeaders($responseHeaders);
        $response->setContent($content);

        $event->setResult($response);
    }

}