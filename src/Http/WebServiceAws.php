<?php

namespace nguyenanhung\Backend\huynq_aws\Http;

use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use nguyenanhung\Validation\Validation;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class WebServiceAws
 *
 * @package   nguyenanhung\Backend\Your_Project\Http
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 */
class WebServiceAws extends BaseHttp
{
    /**
     * @var S3Client
     */
    private $aws;

    /**
     * WebServiceAccount constructor.
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
        $this->aws = new S3Client($options['aws']);

    }

    public function upload(): WebServiceAws
    {
        $inputData = $this->inputData;
        $this->printLogScope(__METHOD__);

        try {
            $isValid = Validation::is_valid(
                $inputData,
                [
                    'Bucket'=>['required'],
                    'Key'=>['required'],
                    'SourceFile'=>['required']
                ],
                [
                    'Bucket'      => ['required' => 'Bucket is required'],
                    'Key'      => ['required' => 'Key is required'],
                    'SourceFile'      => ['required' => 'SourceFile is required'],
                ]
            );
            $this->logger->debug(__METHOD__ . '.' . __LINE__, 'isValid:', $isValid);
            if ($isValid !== true) {
                $this->response = [
                    'status_code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'desc'        => json_encode($isValid),
                    'input_data'  => $this->inputData
                ];
                $this->logger->error(__METHOD__ . '.' . __LINE__, $this->response['desc']);
            }
            else{
                $data = array(
                    'Bucket'     => $inputData['Bucket'],
                    'Key'        => $inputData['Key'],
                    'SourceFile' => $inputData['SourceFile'],
                    'ACL'        => empty($inputData['ACL'])?'':'public-read',
                );
                $result = $this->aws->putObject($data);
                $this->logger->info(__METHOD__ . '.' . __LINE__, 'Success upload image, url : ' . $result['ObjectURL']);

                $this->response = [
                    'status_code' => Response::HTTP_OK,
                    'desc'        => 'upload file success',
                    'url'         => $result['ObjectURL']
                ];
            }
        } catch (S3Exception $e) {
            $this->logger->error(__METHOD__ . '.' . __LINE__, $e->getMessage());

            $this->response = [
                'status_code' => $e->getStatusCode(),
                'desc'        => $e->getMessage(),
            ];
        }

        return $this;
    }

    public function download(): WebServiceAws
    {
        try {
            // Get the object.
            $this->logger->info(__METHOD__ . '.' . __LINE__, 'Get object');
            $result = $this->aws->getObject($this->inputData);

            $path = $this->inputData['path'] . $this->inputData['fileName'];
            $this->logger->debug(__METHOD__ . '.' . __LINE__, 'path', $path);

            $dir = $this->inputData['path'];
            $this->logger->debug(__METHOD__ . '.' . __LINE__, 'dir', $dir);

            $this->handleObject($dir, $path, $result['Body']);

        } catch (S3Exception $e) {
            $this->logger->error(__METHOD__ . '.' . __LINE__, $e->getMessage());

            $this->response = [
                'status_code' => $e->getStatusCode(),
                'desc'        => $e->getMessage(),
            ];
        }

        return $this;
    }

}
