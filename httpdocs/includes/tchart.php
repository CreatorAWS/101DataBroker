<?php

	/*
	 * http://www.chartjs.org/docs/
	 */

	class TChart
		{
			protected $info=Array();
			protected $data=Array();
			protected $dataset=Array();
			protected $labels=Array();
			protected $options=Array();
			protected $current_dataset=0;
			protected $current_index=0;

			protected $color_pattern=Array(
				'#4D4D4D',
				'#5DA5DA',
				'#FAA43A',
				'#60BD68',
				'#F17CB0',
				'#B2912F',
				'#B276B2',
				'#DECF3F',
				'#F15854',
				'#FFFFCC',
			);

			function __construct()
				{
					sm_add_jsfile('https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.1.6/Chart.bundle.min.js', true);
					$this->info['canvas_id']='myChart';
					$this->info['type']='line';
					$this->info['default_color']='#cccccc';
					$this->info['default_opacity']=0.2;
					$this->info['default_border_color']='#000000';
					$this->info['default_border_opacity']=1;
					$this->SetYAxeBeginAtZero();
					$this->options['scales']['xAxes'][0]['position']='bottom';
					$this->current_dataset=-1;
					$this->AddDataset();
				}

			function AddDataset($main_label='')
				{
					$this->current_dataset++;
					$this->SetDatasetMainLabel($main_label);
					$this->dataset[$this->current_dataset]['borderWidth']=1;
				}

			function SwitchDataset($dataset_index='')
				{
					$this->current_dataset=$dataset_index;
					if ($this->current_dataset>=count($this->dataset))
						$this->current_dataset=count($this->dataset)-1;
					if ($this->current_dataset<0)
						$this->current_dataset=0;
				}

			function SetDatasetMainLabel($main_label='')
				{
					$this->dataset[$this->current_dataset]['label']=$main_label;
				}

			function SetDatasetBorderColor($color)
				{
					$this->dataset[$this->current_dataset]['borderColor']=TChart::HexToRGBA($color);
				}

			function SetDatasetFill($fill=true)
				{
					$this->dataset[$this->current_dataset]['fill']=$fill;
				}

			function SetYAxeBeginAtZero($begin_at_zero=true)
				{
					$this->options['scales']['yAxes'][0]['ticks']['beginAtZero']=$begin_at_zero;
				}

			function AddDataPointXY($x, $y)
				{
					$this->AddDataPoint(Array('x'=>$x, 'y'=>$y));
					return $this;
				}

			function AddLabelWithDataPoint($label, $value)
				{
					$this->AddLabel($label);
					$this->AddDataPoint($value);
					return $this;
				}

			function AddDataPoint($value)
				{
					$this->current_index=count($this->dataset[$this->current_dataset]['data']);
					$this->WithValue($value);
					$this->WithColor($this->info['default_color'], $this->info['default_opacity']);
					$this->WithBorder($this->info['default_border_color'], $this->info['default_border_opacity']);
					return $this;
				}

			function AddLabel($value)
				{
					$index=count($this->labels);
					$this->labels[$index]=$value;
					return $this;
				}

			function WithValue($value)
				{
					$this->dataset[$this->current_dataset]['data'][$this->current_index]=$value;
					return $this;
				}

			function WithColor($color_hex, $opacity=1)
				{
					$this->dataset[$this->current_dataset]['backgroundColor'][$this->current_index]=TChart::HexToRGBA($color_hex, $opacity);
					return $this;
				}

			function WithColorFromPattern($opacity=1)
				{
					if (!empty($this->color_pattern[$this->current_index]))
						{
							$this->WithColor($this->color_pattern[$this->current_index], $opacity);
						}
					return $this;
				}

			function WithBorder($color_hex, $opacity=1)
				{
					$this->dataset[$this->current_dataset]['borderColor'][$this->current_index]=TChart::HexToRGBA($color_hex, $opacity);
					return $this;
				}

			function SetTypeLine()
				{
					$this->info['type']='line';
				}

			function SetTypeLineXY()
				{
					$this->info['type']='line';
					$this->options['scales']['xAxes'][0]['type']='linear';
				}

			function SetTypeBar()
				{
					$this->info['type']='bar';
				}

			function SetTypePie()
				{
					$this->info['type']='pie';
				}

			function SetTypeDoughnut()
				{
					$this->info['type']='doughnut';
				}

			function SetTypeRadar()
				{
					$this->info['type']='radar';
				}

			function SetTypePolarArea()
				{
					$this->info['type']='polarArea';
				}

			function SetTypeBubble()
				{
					$this->info['type']='bubble';
				}

			function GetCanvasHTML()
				{
					return '<canvas id="'.$this->info['canvas_id'].'"></canvas>';
				}

			function SetCanvasDOMID($id)
				{
					$this->info['canvas_id']=$id;
				}

			function GetJavascript()
				{
					$str='
							                var myChart = new Chart(document.getElementById("'.jsescape($this->info['canvas_id']).'"), {
												type: \''.jsescape($this->info['type']).'\',
												data: {';
					if (count($this->labels)>0)
						$str.='						labels: '.json_encode($this->labels).',';
					$str.='							datasets: '.json_encode($this->dataset).'
												},';
					$str.='							options: '.json_encode($this->options).'
											});
										';
					return $str;
				}

			public static function HexToRGBA($hex, $opacity=1, $return_javascript_instead_array=true)
				{
					$hex = str_replace("#", "", $hex);
					if ($opacity>1)
						$opacity=1;
					if ($opacity<0)
						$opacity=0;
					if (strlen($hex) == 3)
						{
							$r = hexdec(substr($hex, 0, 1).substr($hex, 0, 1));
							$g = hexdec(substr($hex, 1, 1).substr($hex, 1, 1));
							$b = hexdec(substr($hex, 2, 1).substr($hex, 2, 1));
						}
					else
						{
							$r = hexdec(substr($hex, 0, 2));
							$g = hexdec(substr($hex, 2, 2));
							$b = hexdec(substr($hex, 4, 2));
						}
					$rgba=array($r, $g, $b, $opacity);
					if ($return_javascript_instead_array)
						return $rgb = 'rgba('.implode(',', $rgba).')';
					else
						return $rgba;
				}

		}