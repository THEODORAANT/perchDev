<?php

class PerchTwillio_Customer extends PerchTwillio_Base
{
	protected $factory_classname = 'PerchTwillio_Customers';
	protected $table             = 'twillio_customers';
	protected $pk                = 'customerID';
	protected $index_table       = 'events_index';

	protected $modified_date_column = 'customerUpdated';
	public $deleted_date_column  = 'customerDeleted';

	protected $duplicate_fields  = array('customerFirstName'=>'first_name', 'customerLastName'=>'last_name', 'customerPhone'=>'phone');

	protected $event_prefix = 'twillio.customer';

	public function update($data)
	{


		return parent::update($data);
	}

	public function update_from_form($SubmittedForm)
    {
        $data = [];
        $data['customerDynamicFields'] = PerchUtil::json_safe_encode($SubmittedForm->data);
        $this->update($data);

        // Addresses?

        $data = $SubmittedForm->data;



    }


}
