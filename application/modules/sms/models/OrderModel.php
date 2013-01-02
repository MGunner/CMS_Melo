<?php
class Sms_Model_OrderModel extends Zend_Db_Table_Abstract
{
	protected $_name = 'orders';
	protected $_dependentTables	= array(
		'Sms_Model_DestinationModel',
		'Sms_Model_TransactionModel'
	);
	protected $_referenceMap = array(
		'Users'	=>	array(
			'columns'		=>	array('user_id'),
			'refTableClass'	=>	'Model_UserModel',
			'refColumns'	=>	array('id'),
			'onDelete'		=>	self::RESTRICT,
			'onUpdate'		=>	self::RESTRICT
		)
	);
	
	public function createOrder($customer_id, $user_id, $sms_content, $test_phone)	
	{
		$rowOrder = $this->createRow();
		if ($rowOrder)
		{
			$date = new DateTime();

			$rowOrder->customer_id = $customer_id;
			$rowOrder->user_id = $user_id;
			$rowOrder->sms_content = $sms_content;
			$rowOrder->order_date = $date->getTimestamp();
			$rowOrder->test_phone = $test_phone;
			return $rowOrder->save();
		}
		else
		{
			throw new Zend_Exception("Could not create new order!");
		}
	}

	public static function retrieveOrder($id = null)
	{
		$orderModel = new self();
		
		if (is_null($id))
		{
			$select = $orderModel->select();
			return $orderModel->fetchAll($select);
		}
		else
		{
			return $orderModel->find($id)->current();
		}
	}
	
	public static function retrieveOrderByUserId($id)
	{
		$orderModel = new self();
		$select = $orderModel->select()
			->where('user_id = ?', $id);
		return $orderModel->fetchAll($select);
	}
	
	public function updateOrder($id, $customer_id, $sms_content, $test_phone) 
	{
		$rowOrder = $this->find($id)->current();
		if ($rowOrder)
		{
			$date = new DateTime();
			
			$rowOrder->customer_id = $customer_id;
			$rowOrder->sms_content = $sms_content;
			$rowOrder->order_date = $date->getTimestamp();
			$rowOrder->test_phone = $test_phone;
			return $rowOrder->save();
		}
		else
		{
			throw new Zend_Exception("Could not update the order!");
		}		
	}
	
	public function deleteOrder($id) 
	{
		$rowOrder = $this->find($id)->current();
		if ($rowOrder)
		{
			return $rowOrder->delete();
		}
		else
		{
			throw new Zend_Exception("Could not delete the customer!");
		}	
	}
		
	public function confirmOrder($id) 
	{
		$rowOrder = $this->find($id)->current();
		if ($rowOrder)
		{
			$rowOrder->order_status = 1;
			return $rowOrder->save();
		}
		else
		{
			throw new Zend_Exception("Could not update the order!");
		}		
	}
	
	public function suspendOrder($id)	
	{
		$rowOrder = $this->find($id)->current();
		if ($rowOrder)
		{
			$rowOrder->order_status = 0;
			return $rowOrder->save();
		}
		else
		{
			throw new Zend_Exception("Could not update the order!");
		}			
	}
	
}
