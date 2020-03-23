<?php
declare(strict_type = 1);

namespace App\Services;

/**
 * Class ExecResult
 * @package App\Objects\Service
 */
final class ExecResult
{
    /**
     * @var bool
     */
    private $success = false;

    /**
     * @var string
     */
    private $message = '';

    /**
     * @var array
     */
    private $data = [];

    /**
     * Get new instance
     *
     * @return ExecResult
     */
    public static function instance(): ExecResult
    {
        return new static;
    }

    /**
     * @return bool
     */
    public function getSuccess(): bool
    {
        return $this->success;
    }

    /**
     * @param bool $success
     * @return ExecResult
     */
    public function setSuccess(bool $success = true): ExecResult
    {
        $this->success = $success;
        return $this;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return ExecResult
     */
    public function setMessage(string $message = ''): ExecResult
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getData(string $key = '')
    {
        if (!empty($key)) {
            return $this->data[$key] ?? $this->data;
        }

        return $this->data;
    }

    /**
     * @param array $data
     * @return ExecResult
     */
    public function setData(array $data = []): ExecResult
    {
        $this->data = $data;
        return $this;
    }
}
