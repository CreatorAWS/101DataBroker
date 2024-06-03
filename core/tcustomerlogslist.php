<?php

if (!defined("TCustomerLogsList_DEFINED"))
{
    /**
     * @method TCustomerLog Item($index)
     */
    Class TCustomerLogsList extends TGenericList
    {
        /** @var TCustomerLog[] $items */
        public $items;
        protected $tablename='customer_log';
        protected $idfield='id';

        function __construct()
        {
            parent::__construct();
        }

        function SetFilterCompany($company_or_id)
        {
            if (!empty($this->sql))
                $this->sql .= ' AND ';
            $this->sql .= ' id_company='.Cleaner::IntObjectID($company_or_id);
        }

        function SetFilterCustomer($customer)
        {
            if (!empty($this->sql))
                $this->sql .= ' AND ';
            $this->sql .= " id_customer=".Cleaner::IntObjectID($customer);
        }

        function SetFilterIsScheduled()
        {
            if (!empty($this->sql))
                $this->sql .= ' AND ';
            $this->sql .= " scheduledtime>".time();
        }

        function SetFilterExcludeScheduled()
        {
            if (!empty($this->sql))
                $this->sql .= ' AND ';
            $this->sql .= " scheduledtime <=".time();
        }

        function SetFilterIncoming()
        {
            $filters = Array('incoming_email', 'incoming_sms', 'incoming_call');
            $this->SetFilterStrValues('action', $filters);
        }

        function SetFilterOutgoing()
        {
            $filters = Array('email', 'bulk_email', 'sms', 'bulk_sms', 'call');
            $this->SetFilterStrValues('action', $filters);
        }

        function SetFilterDay()
        {
            if (!empty($this->sql))
                $this->sql.=' AND ';
            $this->sql.=" addedtime >=".SMDateTime::DayStart(time()). " AND addedtime<=".SMDateTime::DayEnd(time());
        }

        function SetFilter7days()
        {
            if (!empty($this->sql))
                $this->sql.=' AND ';
            $this->sql.=" addedtime >= ".SMDateTime::DayStart(time()-7*24*3600). " AND addedtime<=".time();
        }
        function SetFilter14days()
        {
            if (!empty($this->sql))
                $this->sql.=' AND ';
            $this->sql.=" addedtime >= ".SMDateTime::DayStart(time()-14*24*3600). " AND addedtime<=".time();
        }

        function SetFilter30days()
        {
            if (!empty($this->sql))
                $this->sql.=' AND ';
            $this->sql.=" addedtime >= ".SMDateTime::DayStart(time()-30*24*3600). " AND addedtime<=".time();
        }

        function SetFilterActionType($action)
        {
            if ( $action == 'email')
                $filters = Array('email', 'bulk_email', 'incoming_email');
            elseif ( $action == 'sms')
                $filters = Array('sms', 'bulk_sms', 'incoming_sms');
            elseif ( $action == 'call')
                $filters = Array('call', 'incoming_call');
            elseif ( $action == 'note')
                $filters = Array('note');
            elseif ( $action == 'task')
                $filters = Array('task');
            elseif ( $action == 'booked_call')
                $filters = Array('booked_call');

            $this->SetFilterStrValues('action', $filters);
        }

        function SetFilterCustomerIDs($arrayids)
        {
            if (!empty($this->sql))
                $this->sql .= ' AND ';
            if (!is_array($arrayids) || count($arrayids)==0)
                $this->sql .= " 1=2 ";
            else
                $this->sql .= " id_customer IN (".implode(',', Cleaner::ArrayIntval($arrayids)).") ";
        }

        function SetFilterObjectsIDs($arrayids)
        {
            if (!empty($this->sql))
                $this->sql .= ' AND ';
            if (!is_array($arrayids) || count($arrayids)==0)
                $this->sql .= " 1=2 ";
            else
                $this->sql .= " id_object IN (".implode(',', Cleaner::ArrayIntval($arrayids)).") ";
        }

        function SetFilterExcludeObjectsIDs($arrayids)
        {
            if (!empty($this->sql))
                $this->sql .= ' AND ';
            if (!is_array($arrayids))
                $this->sql .= " 1=2 ";
            else
                $this->sql .= " id_object NOT IN (".implode(',', Cleaner::ArrayIntval($arrayids)).") ";
        }

        function SetFilterIsContactAction()
        {
            $this->SetFilterStrValues('action', Array(
                'sms',
                'bulk_sms',
                'email',
                'bulk_email',
                'call',
                'booked_call'
            ));
        }

        function SetFilterPointOfContactAction()
        {
            $this->SetFilterStrValues('action', Array(
                'sms',
                'bulk_sms',
                'email',
                'bulk_email',
                'call',
                'incoming_call',
                'incoming_sms',
                'incoming_email'
            ));
        }

        function SetFilterIsCampaign()
        {
            $this->SetFilterStrValues('action', Array(
                'start_campaign'
            ));
        }

        function SetFilterIsNotScheduled()
        {
            if (!empty($this->sql))
                $this->sql .= ' AND ';
            $this->sql .= " scheduledtime = 0";
        }

        function OrderByAddedTime($asc=true)
        {
            $this->orderby='addedtime';
            if (!$asc)
                $this->orderby.=' DESC';
            return $this;
        }

        function SetFilterEmployee($employee)
        {
            if (!empty($this->sql))
                $this->sql .= ' AND ';
            $this->sql .= " id_employee=".Cleaner::IntObjectID($employee);
        }

        protected function InitItem($index)
        {
            $item = new TCustomerLog($this->itemsinfo[$index]);
            return $item;
        }

        function SetFilterCustomPeriod(int $start_period, int $end_period): self
        {
            $this->SetFilterIntValuesBetween('addedtime', $start_period, SMDateTime::DayEnd($end_period));
            return $this;
        }
    }

    define("TCustomerLogsList_DEFINED", 1);
}
