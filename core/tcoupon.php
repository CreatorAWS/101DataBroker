<?php

if (!defined("TCoupon_DEFINED"))
	{
		class TCoupon
			{
				var $info;
				function TCoupon($id_or_cahcedinfo=NULL)
					{
						global $sm;
						if (is_array($id_or_cahcedinfo))
							{
								$this->info=$id_or_cahcedinfo;
							}
						else
							{
								$this->info=TQuery::ForTable('coupons')->Add('id', intval($id_or_cahcedinfo))->Get();
							}
					}
				public static function initWithCode($code, $company_id)
					{
						$info=TQuery::ForTable('coupons')
							->Add('id_u', intval($company_id))
							->Add("UPPER(code)='".dbescape(strtoupper($code))."'")
							->Get();
						$coupon=new TCoupon($info);
						return $coupon;
					}
				function IncreaseUsage()
					{
						TQuery::ForTable('coupons')
							->Add('used_times=used_times+1')
							->Update('id', intval($this->ID()));
					}
				function ID()
					{
						return intval($this->info['id']);
					}
				function CodeUppercased()
					{
						return strtoupper($this->info['code']);
					}
				function CompanyID()
					{
						return intval($this->info['id_u']);
					}
				function Title()
					{
						return $this->info['title'];
					}
				function isDiscountFixed()
					{
						return $this->info['discount_fixed']>0;
					}
				function DiscountFixed()
					{
						return round(floatval($this->info['discount_fixed']), 2);
					}
				function isDiscountPercent()
					{
						return $this->info['discount_percent']>0;
					}
				function DiscountPercent()
					{
						return round(floatval($this->info['discount_percent']), 2);
					}
				function MaxDiscount()
					{
						return round(floatval($this->info['max_discount']), 2);
					}
				function Exists()
					{
						return !empty($this->info['id']);
					}
				function CalculateDiscount($sum)
					{
						$sum=floatval($sum);
						$discount=$sum*$this->DiscountPercent()/100+$this->DiscountFixed();
						if ($this->MaxDiscount()!=0 && $discount>$this->MaxDiscount())
							$discount=$this->MaxDiscount();
						if ($discount>$sum)
							$discount=$sum;
						return round($discount, 2);
					}
				function CalculateCouponDiscount($sum)
					{
						$sum=floatval($sum);
						$discount=$sum*$this->DiscountPercent()/100+$this->DiscountFixed();
						if ($this->MaxDiscount()!=0 && $discount>$this->MaxDiscount())
							$discount=$this->MaxDiscount();
						if ($discount>$sum)
							$discount=$sum;
						return round($discount, 2);
					}
				function CalculateDiscountInCents($sum)
					{
						return intval($this->CalculateDiscount($sum)*100);
					}
				function isApplicableForProduct($product)
					{
						if (empty($this->info['assigned_to_product']) && empty($this->info['stripe_coupon_id']))
							return true;
						if (is_object($product))
							$product=$product->ID();
						if (intval($product)==intval($this->info['assigned_to_product']))
							return true;
						return false;
					}
				function StripeCouponID()
					{
						return $this->info['stripe_coupon_id'];
					}
				function GetUsageCount()
					{
						return $this->info['used_times'];
					}
				function GetDuration()
					{
						if ($this->info['duration'] == 1)
							return 'once';
						else
							return 'forever';
					}
				function SetDuration($val)
					{
						$this->UpdateValues(Array('duration' => intval($val)));
					}
				function HasStripeCouponID()
					{
						return !empty($this->StripeCouponID());
					}
				function SetStripeCouponID($val)
					{
						$this->UpdateValues(Array('stripe_coupon_id' => $val));
					}
				function CreateStripeCoupon()
					{
						if ($this->isDiscountFixed())
							{
								$stripe_coupon=\Stripe\Coupon::create(array(
									'id' => $this->CodeUppercased(),
									'amount_off' => intval($this->DiscountFixed()*100),
									'currency' => 'USD',
									'name' => $this->Title(),
									'duration' => $this->GetDuration()
								));
							}
						elseif ($this->isDiscountPercent())
							{
								$stripe_coupon=\Stripe\Coupon::create(array(
									'id' => $this->CodeUppercased(),
									'percent_off' => $this->DiscountPercent(),
									'name' => $this->Title(),
									'duration' => $this->GetDuration()
								));
							}
						$this->SetStripeCouponID($stripe_coupon->id);
					}
				function UpdateStripeCoupon()
					{
						if ($this->HasStripeCouponID())
							{
								$coupon = \Stripe\Coupon::update(
									$this->StripeCouponID(), array('name' => $this->Title())
								);
							}
					}
				function RemoveStripeCoupon()
					{
						if ($this->HasStripeCouponID())
							{
								$coupon = \Stripe\Coupon::retrieve($this->StripeCouponID());
								$coupon->delete();
							}
					}
				function UpdateValues($params)
					{
						if (!is_array($params))
							return;
						unset($params['id']);
						if (empty($params))
							return;
						$q = new TQuery('coupons');
						foreach ($params as $key => $val)
							{
								$this->info[$key] = $val;
								$q->Add($key, dbescape($this->info[$key]));
							}
						$q->Update('id', $this->ID());
					}
				public static function isApplicableCouponForProductExists($product, $company_id)
					{
						if (is_object($product))
							$product=$product->ID();
						return intval(
								TQuery::ForTable('coupons')
									->Add('assigned_to_product=0 OR assigned_to_product='.intval($product))
									->GetField('id')
							)>0;
					}
				function isApplicableForPlan($plan)
					{
						if (empty($this->info['assigned_to_plan']) && !empty($this->info['stripe_coupon_id']))
							return true;
						if (is_object($plan))
							$plan=$plan->ID();
						if (intval($plan)==intval($this->info['assigned_to_plan']))
							return true;
						return false;
					}
				public static function isApplicableCouponForPlanExists($plan, $company_id)
					{
						if (is_object($plan))
							$plan=$plan->ID();
						return intval(
								TQuery::ForTable('coupons')
									->Add('assigned_to_plan=0 OR assigned_to_plan='.intval($plan))
									->GetField('id')
							)>0;
					}
			}
		define("TCoupon_DEFINED", 1);
	}
	
?>