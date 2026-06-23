<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (! $session->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        if (! empty($arguments)) {
            $role = $session->get('role');

            if (! in_array($role, $arguments)) {
                return redirect()->to('/dashboard')->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
            }
        }

        // PHP's file-based session handler keeps an exclusive lock on the
        // session file for the entire request. Pages here can run slow
        // map/chart/datatable queries, and without releasing the lock early,
        // any other tab/request sharing the same session (cookie) queues
        // behind it instead of loading. logout() still needs the session
        // open so it can destroy it.
        if ($request instanceof IncomingRequest && $request->getPath() !== 'logout') {
            $session->close();
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
