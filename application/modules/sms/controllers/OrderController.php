<?php
class Sms_OrderController extends Zend_Controller_Action
{
	public function indexAction() 
	{
		$orders = Sms_Model_OrderModel::retrieveOrder();
		if ($orders->count() > 0)
		{
			$this->view->orders = $orders->toArray();
		}
		else
		{
			$this->view->orders = null;
		}
	}
	
	public function createOrderAction()
	{
		$form = new Sms_Form_OrderCreateForm;
		
		if ($this->getRequest()->isPost())
		{
			if ($form->isValid($_POST))
			{
				$data = $form->getValues();
				$orderModel = new Sms_Model_OrderModel;
				$id = $orderModel->createOrder(
					$data['customer_id'],
					$data['sms_content'],
					$data['test_phone']
				);
				
				return $this->_forward('index');
			}
		}
		
		$this->view->form = $form;	
	}
	
	public function updateOrderAction() 
	{
		$form = new Sms_Form_OrderCreateForm;
		$orderModel = new Sms_Model_OrderModel;
		
		if ($this->getRequest()->isPost())
		{
			if ($form->isValid($_POST))
			{
				$data = $form->getValues();
				$id = $orderModel->updateOrder(
					$data['id'],
					$data['customer_id'],
					$data['sms_content'],
					$data['test_phone']		
				);
				
				return $this->_forward('index');
			}
		}
		else
		{
			$requestedId = $this->_request->getParam('id');
			$requestedOrder = $orderModel->find($requestedId)->current();
			$form->populate($requestedOrder->toArray());
		}
		$this->view->form = $form;		
	}
	
	public function deleteOrderAction() 
	{
		$requestedId = $this->_request->getParam('id');
		$orderModel = new Sms_Model_OrderModel;
		$id = $orderModel->deleteOrder($requestedId);
		
		return $this->_forward('index');	
	}	

	public function listDestinationAction()
	{
		$requestedId = $this->_request->getParam('id');
		$destinationModel = new Sms_Model_DestinationModel;
		$destinations = $destinationModel->retrieveDestination($requestedId);
		if ($destinations->count() > 0)
		{
			$this->view->destinations = $destinations->toArray();
		}
		else
		{
			$this->view->destinations = null;
		}
		
		// send order_id to view
		$this->view->order_id = $requestedId;
	}
	
	// note: destination quantity has to be calculate (!!)
	public function createDestinationAction()	
	{
		$form = new Sms_Form_OrderDestinationForm;
		
		if ($this->getRequest()->isPost())
		{
			if ($form->isValid($_POST))
			{
				$data = $form->getValues();
				$destinationModel = new Sms_Model_DestinationModel;
				$requestedId = $this->_request->getParam('id');
				$id = $destinationModel->createDestination(
					$requestedId,
					$data['destination_type'],
					$data['destination_value'],
					$data['dispatch_date'],
					'N/A'
				);
				
				return $this->_forward('list-destination');
			}
		}
		
		$this->view->form = $form;			
	}
	
	public function updateDestinationAction() 
	{
		$form = new Sms_Form_OrderDestinationForm;
		$destinationModel = new Sms_Model_DestinationModel;
		
		if ($this->getRequest()->isPost())
		{
			if ($form->isValid($_POST))
			{
				$data = $form->getValues();
				$id = $destinationModel->updateDestination(
					$data['id'],
					$data['order_id'],
					$data['destination_type'],
					$data['destination_value'],
					$data['dispatch_date'],
					'N/A'					
				);
				
				// return $this->_forward($action, $controller = null, $module = null, array($params = null))
				return $this->_forward('list-destination', null, null, array('id' => $data['order_id']));
			}
		}
		else
		{
			$requestedId = $this->_request->getParam('id');
			$requestedDestination = $destinationModel->find($requestedId)->current();
			$form->populate($requestedDestination->toArray());
			
			// meanwhile; format the date field
			$timeStampDate = $form->getValue('dispatch_date');
			$form->getElement('dispatch_date')->setValue(date('m-d-Y', $timeStampDate));
		}
		$this->view->form = $form;		
	}
	
	public function deleteDestinationAction() 
	{
		$requestedId = $this->_request->getParam('id');
		$destinationModel = new Sms_Model_DestinationModel;
		$returnedValue = $destinationModel->deleteDestination($requestedId);
		$id = $returnedValue['destination_id'];
		$order_id = $returnedValue['order_id'];
		
		return $this->_forward('list-destination', null, null, array('id' => $order_id));
	}
	
	public function reviewOrderAction() 
	{
	}

}


























