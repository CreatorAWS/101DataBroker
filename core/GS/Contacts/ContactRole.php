<?php

namespace GS\Contacts;

class ContactRole
{

    public const CEO = 'CEO';
    public const COO = 'COO';
    public const CFO = 'CFO';
    public const CMO = 'CMO';
    public const CTO = 'CTO';
    public const CPA = 'CPA';
    public const OWNER = 'Owner';
    public const FOUNDER = 'Founder';
    public const CO_FOUNDER = 'Co-Founder';
    public const VICE_PRESIDENT = 'Vice President';
    public const EXECUTIVE_ASSISTANT = 'Executive Assistant';
    public const MARKETING = 'Marketing';
    public const SALES = 'Sales';
    public const PRODUCT = 'Product';
    public const PROJECT_MANAGER = 'Project Manager';
    public const FINANCE = 'Finance';
    public const HUMAN_RESOURCES = 'Human Resources';

    public static function ListAvailable(): array
    {
        return [
            self::CEO,
            self::COO,
            self::CFO,
            self::CMO,
            self::CTO,
            self::CPA,
            self::OWNER,
            self::FOUNDER,
            self::CO_FOUNDER,
            self::VICE_PRESIDENT,
            self::EXECUTIVE_ASSISTANT,
            self::MARKETING,
            self::SALES,
            self::PRODUCT,
            self::PROJECT_MANAGER,
            self::FINANCE,
            self::HUMAN_RESOURCES,
        ];
    }
}
