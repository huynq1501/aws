<?php

namespace nguyenanhung\Backend\huynq_aws\Http;

use nguyenanhung\Backend\huynq_aws\Base\BaseCore;
use nguyenanhung\Backend\huynq_aws\Database\Database;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class BaseHttp
 *
 * @package   nguyenanhung\Backend\Your_Project\Http
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 */
class BaseHttp extends BaseCore
{

    /** @var Database */
    protected $db;

    /**
     * BaseHttp constructor.
     *
     * @param array $options
     *
     * @author   : 713uk13m <dev@nguyenanhung.com>
     * @copyright: 713uk13m <dev@nguyenanhung.com>
     */
    public function __construct(array $options = array())
    {
        parent::__construct($options);
        $this->logger->setLoggerSubPath(__CLASS__);
        $this->db = new Database($options);
    }

    public function handleObject($dir, $name, $data): void
    {
        $checkDirExists = $this->ensureDirExists($name);
        if ($checkDirExists && is_writable($dir)) {
            file_put_contents($name, $data);
            $this->response = [
                'status_code' => Response::HTTP_OK,
                'desc'        => 'download file success',
                'path'        => $name
            ];

            $this->logger->debug(__METHOD__ . '.' . __LINE__, $this->response['desc'] . ' in: ', $name);

        } else {
            $this->response = [
                'status_code' => Response::HTTP_FORBIDDEN,
                'desc'        => 'can not create directory',
            ];

            $this->logger->error(__METHOD__ . '.' . __LINE__, $this->response['desc']);
        }

    }

    public function ensureDirExists($name): bool
    {
        $dir = $name;
        if (substr($name, -1, 1) !== '/') {
            $parts = explode('/', $name);
            array_pop($parts);
            $dir = implode('/', $parts);
        }
        return !(!file_exists($dir) && !mkdir($dir, 0777, true) && !is_dir($dir));

    }

    protected function printLogScope(string $name): void
    {
        $halfPartStr = '====================';
        $this->logger->info('', $halfPartStr . $name . $halfPartStr);
    }
}
