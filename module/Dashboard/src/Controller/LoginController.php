<?php

namespace Dashboard\Controller;

use Zend\Authentication\Adapter\DbTable\CredentialTreatmentAdapter as AuthAdapter;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Result;
use Zend\Db\Adapter\Adapter;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\SessionManager;

use Zend\View\Model\ViewModel;

class LoginController extends AbstractActionController
{
	private $mysqlAdp;

	public function __construct(Adapter $mysqlAdp)
	{
		$this->mysqlAdp = $mysqlAdp;
	}

	public function indexAction()
	{
		$this->layout('layout/login');
		$auth = new AuthenticationService();
		$viewmodel = new ViewModel();
		$request = $this->getRequest();
		$message = 'Ingrese sus Datos'; //Message

		if ($auth->hasIdentity()) {
			$auth->clearIdentity();
			$sessionManager = new SessionManager();
			$sessionManager->forgetMe();
		}

		if ($request->isPost()) {
			$dataForm = $request->getPost();
			if (!empty($dataForm->usuario) && !empty($dataForm->clave)) {
				try {
					$authAdapter = new AuthAdapter($this->mysqlAdp, 'user', 'username', 'password', 'sha1(?) AND active = 1');
					$auth->setAdapter($authAdapter);
					$auth->getAdapter()->setIdentity(strtolower($dataForm->usuario))->setCredential(strtolower($dataForm->clave));
					$result = $auth->authenticate();
				} catch (\Exception $e) {
					var_dump($e->getMessage());
					exit;
				}
				switch ($result->getCode()) {
					case Result::SUCCESS:
						$storage = $auth->getStorage();
						$userData = $authAdapter->getResultRowObject(null, 'password');
						$userData->empcod = '';
						$storage->write($userData);

						if (!empty($dataForm->rememberMe)) {
							$sessionManager = new SessionManager();
							$sessionManager->rememberMe(99999999);
						}
						$this->flashMessenger()->clearMessages();

						return $this->redirect()->toRoute('dashboard-index');

						break;
					case Result::FAILURE_UNCATEGORIZED:
						$message = "El usuario ingresado no tiene acceso.";
						break;
					default :
						$message = "Usuario y/o clave incorrecto.";
						break;
				}
			}

			$this->flashMessenger()->addWarningMessage(['Atención', $message]);
		}

		return $viewmodel;
	}

	public function logoutAction()
	{
		$auth = new AuthenticationService();

		if ($auth->hasIdentity()) {
			$auth->clearIdentity();
			$sessionManager = new SessionManager();
			$sessionManager->forgetMe();
		}

		$this->redirect()->toRoute('dashboard-login');
	}
}
