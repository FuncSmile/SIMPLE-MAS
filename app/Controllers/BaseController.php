<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseController extends Controller
{
    protected $session;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        $this->helpers = ['form', 'url', 'html'];

        parent::initController($request, $response, $logger);

        $this->session = service('session');
    }

    protected function isLoggedIn(): bool
    {
        return $this->session->get('isLoggedIn') === true;
    }

    protected function getUserId(): ?int
    {
        return $this->session->get('user_id');
    }

    protected function getUserRole(): ?string
    {
        return $this->session->get('role');
    }

    protected function isAdmin(): bool
    {
        return in_array($this->getUserRole(), ['admin_instansi', 'super_admin']);
    }

    protected function isSuperAdmin(): bool
    {
        return $this->getUserRole() === 'super_admin';
    }
}
