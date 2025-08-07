<?php

namespace App\Services;

use App\Models\MikrotikConfig;
use Exception;

/**
 * Mikrotik RouterOS API service for managing PPPoE connections.
 */
class MikrotikService
{
    /**
     * Create a new service instance.
     *
     * @param MikrotikConfig|null $config
     */
    public function __construct(?MikrotikConfig $config = null)
    {
        $this->config = $config ?: MikrotikConfig::active()->first();
    }
    /**
     * Socket connection to Mikrotik router.
     *
     * @var \Socket|null
     */
    private $socket;
    
    /**
     * Active Mikrotik configuration.
     *
     * @var MikrotikConfig|null
     */
    private $config;

    
    /**
     * Connect to Mikrotik router.
     *
     * @return bool
     */
    public function connect(): bool
    {
        if (!$this->config) {
            throw new Exception('No active Mikrotik configuration found.');
        }
        
        try {
            $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
            
            if (!$this->socket) {
                throw new Exception('Failed to create socket.');
            }
            
            $result = socket_connect($this->socket, $this->config->host, $this->config->port);
            
            if (!$result) {
                throw new Exception('Failed to connect to Mikrotik.');
            }
            
            // Login
            $this->write('/login');
            $this->write('=name=' . $this->config->username);
            $this->write('=password=' . $this->config->password);
            $this->write('');
            
            $response = $this->read();
            
            if (strpos($response[0], '!done') === false) {
                throw new Exception('Login failed.');
            }
            
            return true;
            
        } catch (Exception $e) {
            $this->disconnect();
            throw $e;
        }
    }
    
    /**
     * Disconnect from Mikrotik router.
     */
    public function disconnect(): void
    {
        if ($this->socket) {
            socket_close($this->socket);
            $this->socket = null;
        }
    }
    
    /**
     * Add PPPoE secret.
     *
     * @param array $data
     * @return bool
     */
    public function addPPPoESecret(array $data): bool
    {
        try {
            $this->connect();
            
            $this->write('/ppp/secret/add');
            $this->write('=name=' . $data['username']);
            $this->write('=password=' . $data['password']);
            $this->write('=service=pppoe');
            $this->write('=profile=' . ($data['profile'] ?? 'default'));
            
            if (!empty($data['remote_address'])) {
                $this->write('=remote-address=' . $data['remote_address']);
            }
            
            if (!empty($data['comment'])) {
                $this->write('=comment=' . $data['comment']);
            }
            
            $this->write('');
            
            $response = $this->read();
            
            return strpos($response[0], '!done') !== false;
            
        } catch (Exception $e) {
            return false;
        } finally {
            $this->disconnect();
        }
    }
    
    /**
     * Remove PPPoE secret.
     *
     * @param string $username
     * @return bool
     */
    public function removePPPoESecret(string $username): bool
    {
        try {
            $this->connect();
            
            // Find secret ID
            $secretId = $this->findSecretId($username);
            
            if (!$secretId) {
                return false;
            }
            
            $this->write('/ppp/secret/remove');
            $this->write('=.id=' . $secretId);
            $this->write('');
            
            $response = $this->read();
            
            return strpos($response[0], '!done') !== false;
            
        } catch (Exception $e) {
            return false;
        } finally {
            $this->disconnect();
        }
    }
    
    /**
     * Enable/disable PPPoE secret.
     *
     * @param string $username
     * @param bool $enabled
     * @return bool
     */
    public function togglePPPoESecret(string $username, bool $enabled): bool
    {
        try {
            $this->connect();
            
            $secretId = $this->findSecretId($username);
            
            if (!$secretId) {
                return false;
            }
            
            $command = $enabled ? '/ppp/secret/enable' : '/ppp/secret/disable';
            
            $this->write($command);
            $this->write('=.id=' . $secretId);
            $this->write('');
            
            $response = $this->read();
            
            return strpos($response[0], '!done') !== false;
            
        } catch (Exception $e) {
            return false;
        } finally {
            $this->disconnect();
        }
    }
    
    /**
     * Get active PPPoE connections.
     *
     * @return array
     */
    public function getActiveConnections(): array
    {
        try {
            $this->connect();
            
            $this->write('/ppp/active/print');
            $this->write('');
            
            $response = $this->read();
            $connections = [];
            
            foreach ($response as $line) {
                if (strpos($line, '=name=') !== false) {
                    $data = $this->parseResponse($response);
                    $connections[] = $data;
                }
            }
            
            return $connections;
            
        } catch (Exception $e) {
            return [];
        } finally {
            $this->disconnect();
        }
    }
    
    /**
     * Test connection to Mikrotik.
     *
     * @return array
     */
    public function testConnection(): array
    {
        try {
            $this->connect();
            
            $this->write('/system/identity/print');
            $this->write('');
            
            $response = $this->read();
            
            return [
                'success' => true,
                'message' => 'Connection successful',
                'response' => $response,
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'response' => null,
            ];
        } finally {
            $this->disconnect();
        }
    }
    
    /**
     * Find secret ID by username.
     *
     * @param string $username
     * @return string|null
     */
    public function findSecretId(string $username): ?string
    {
        $this->write('/ppp/secret/print');
        $this->write('?name=' . $username);
        $this->write('');
        
        $response = $this->read();
        
        foreach ($response as $line) {
            if (strpos($line, '=.id=') !== false) {
                return substr($line, 4);
            }
        }
        
        return null;
    }
    
    /**
     * Write data to socket.
     *
     * @param string $data
     */
    public function write(string $data): void
    {
        $length = strlen($data);
        
        if ($length < 0x80) {
            $length_encoded = chr($length);
        } elseif ($length < 0x4000) {
            $length_encoded = chr($length | 0x80) . chr($length >> 7);
        } else {
            $length_encoded = chr($length | 0xC0) . chr($length >> 6) . chr($length >> 14);
        }
        
        socket_write($this->socket, $length_encoded . $data);
    }
    
    /**
     * Read data from socket.
     *
     * @return array
     */
    public function read(): array
    {
        $response = [];
        
        while (true) {
            $length = $this->readLength();
            
            if ($length === 0) {
                break;
            }
            
            $data = socket_read($this->socket, $length);
            $response[] = $data;
            
            if ($data === '!done') {
                break;
            }
        }
        
        return $response;
    }
    
    /**
     * Read length from socket.
     *
     * @return int
     */
    public function readLength(): int
    {
        $byte = socket_read($this->socket, 1);
        
        if (!$byte) {
            return 0;
        }
        
        $byte = ord($byte);
        
        if ($byte < 0x80) {
            return $byte;
        } elseif ($byte < 0xC0) {
            return (($byte & 0x7F) << 8) + ord(socket_read($this->socket, 1));
        } else {
            return (($byte & 0x3F) << 16) + (ord(socket_read($this->socket, 1)) << 8) + ord(socket_read($this->socket, 1));
        }
    }
    
    /**
     * Parse Mikrotik response.
     *
     * @param array $response
     * @return array
     */
    public function parseResponse(array $response): array
    {
        $data = [];
        
        foreach ($response as $line) {
            if (strpos($line, '=') === 0) {
                $parts = explode('=', substr($line, 1), 2);
                if (count($parts) === 2) {
                    $data[$parts[0]] = $parts[1];
                }
            }
        }
        
        return $data;
    }
}