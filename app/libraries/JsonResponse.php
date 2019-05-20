<?php

use Phalcon\Http\Response as PhalconResponse;

/**
 * Class Response
 * @author Adeyemi Olaoye <yemi@cottacush.com>
 */
class JsonResponse extends PhalconResponse
{
    const RESPONSE_TYPE_ERROR = 'error';
    const RESPONSE_TYPE_SUCCESS = 'success';
    const RESPONSE_TYPE_FAIL = 'fail';

    public function __construct($content = null, $code = null, $status = null)
    {
        parent::__construct($content, $code, $status);
        $this->setHeader('Access-Control-Allow-Origin', '*')
            ->setHeader('Access-Control-Allow-Methods', 'GET,PUT,POST,DELETE,OPTIONS')
            ->setHeader('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Range, Content-Disposition, Content-Type, Authorization, application/json')
            ->setHeader('Access-Control-Allow-Credentials', 'true');
    }

    /**
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $code
     * @param int|string $http_status_code
     * @param null $message
     * @return mixed
     */
    public function sendError($code, $message, $http_status_code = 401)
    {
        $response = array(
            'status'    => self::RESPONSE_TYPE_ERROR,
            'message'   => $message,
            'code'      => $code
        );

        $this->setStatusCode($http_status_code, HttpStatusCodes::getMessage($http_status_code))->sendHeaders();
        $this->setJsonContent($response);
        return $this->sendResponse();
    }


    /**
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $data
     * @return mixed
     */
    public function sendSuccess($data)
    {
        $data = (array) $data;
        $this->setStatusCode(200, HttpStatusCodes::getMessage(200))->sendHeaders();
        $this->setJsonContent([
            "status" => self::RESPONSE_TYPE_SUCCESS,
            "data"   => $data
        ]);
        return $this->sendResponse();
    }

    /**
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $data
     * @param int|string $http_status_code
     * @return mixed|void
     */
    public function sendFail($data, $http_status_code = 401)
    {
        $data = (array) $data;
        $data["status"] = self::RESPONSE_TYPE_FAIL;
        $this->setStatusCode($http_status_code, HttpStatusCodes::getMessage($http_status_code))->sendHeaders();
        $this->setJsonContent($data);
        $this->sendResponse();
    }

    /**
     * Send response
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     */
    public function sendResponse()
    {
        $this->setContentType("application/json");
        if (!$this->isSent()) {
            $this->send();
        }
    }
}
