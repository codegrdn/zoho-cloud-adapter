<?php
/**
 * Created by PhpStorm.
 * User: sion
 * Date: 06.09.2017
 * Time: 14:55
 */

namespace App\Dropbox\Classes\Traits;


trait MapperTrait {
	public $modules = [];
	static $auto_increment = 1;

	public function getMapHeading() {
		return [
			'Opp #' => [
				'width' => 6,
				'styles' => [
					"font" => [
						"size" => 9,
						'color' => ['rgb' => 'FFFFFF'],
						"bold" => true
					],
					'alignment' => [
						'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					],
					'fill' => [
						'type' => \PHPExcel_Style_Fill::FILL_SOLID,
						'color' => ['rgb' => '4472c4']
					],
					'borders' => [
						'outline' => [
							'style' => \PHPExcel_Style_Border::BORDER_THICK,
							'color' => array('rgb' => '000000'),
						],
					],
				]
			],
			'Company' => [
				'width' => 10,
				'styles' => [
					"font" => [
						"size" => 9,
						'color' => ['rgb' => 'FFFFFF'],
						"bold" => true
					],
					'alignment' => [
						'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					],
					'fill' => [
						'type' => \PHPExcel_Style_Fill::FILL_SOLID,
						'color' => ['rgb' => '4472c4']
					],
					'borders' => [
						'outline' => [
							'style' => \PHPExcel_Style_Border::BORDER_THICK,
							'color' => array('rgb' => '000000'),
						],
					],
				]
			],
			'Sales person' => [
				'width' => 20,
				'styles' => [
					"font" => [
						"size" => 9,
						'color' => ['rgb' => 'FFFFFF'],
						"bold" => true
					],
					'alignment' => [
						'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					],
					'fill' => [
						'type' => \PHPExcel_Style_Fill::FILL_SOLID,
						'color' => ['rgb' => '4472c4']
					],
					'borders' => [
						'outline' => [
							'style' => \PHPExcel_Style_Border::BORDER_THICK,
							'color' => array('rgb' => '000000'),
						],
					],
				]
			],
			'Customer Name' => [
				'width' => 20,
				'styles' => [
					"font" => [
						"size" => 9,
						'color' => ['rgb' => 'FFFFFF'],
						"bold" => true
					],
					'alignment' => [
						'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					],
					'fill' => [
						'type' => \PHPExcel_Style_Fill::FILL_SOLID,
						'color' => ['rgb' => '4472c4']
					],
					'borders' => [
						'outline' => [
							'style' => \PHPExcel_Style_Border::BORDER_THICK,
							'color' => array('rgb' => '000000'),
						],
					],
				]
			],
			'Product' => [
				'width' => 25,
				'styles' => [
					"font" => [
						"size" => 9,
						'color' => ['rgb' => 'FFFFFF'],
						"bold" => true
					],
					'alignment' => [
						'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					],
					'fill' => [
						'type' => \PHPExcel_Style_Fill::FILL_SOLID,
						'color' => ['rgb' => '4472c4']
					],
					'borders' => [
						'outline' => [
							'style' => \PHPExcel_Style_Border::BORDER_THICK,
							'color' => array('rgb' => '000000'),
						],
					],
				]
			],
			'Segment' => [
				'width' => 13,
				'styles' => [
					"font" => [
						"size" => 9,
						'color' => ['rgb' => 'FFFFFF'],
						"bold" => true
					],
					'alignment' => [
						'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					],
					'fill' => [
						'type' => \PHPExcel_Style_Fill::FILL_SOLID,
						'color' => ['rgb' => '4472c4']
					],
					'borders' => [
						'outline' => [
							'style' => \PHPExcel_Style_Border::BORDER_THICK,
							'color' => array('rgb' => '000000'),
						],
					],
				]
			],
			'Business Line' => [
				'width' => 15,
				'styles' => [
					"font" => [
						"size" => 9,
						'color' => ['rgb' => 'FFFFFF'],
						"bold" => true
					],
					'alignment' => [
						'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					],
					'fill' => [
						'type' => \PHPExcel_Style_Fill::FILL_SOLID,
						'color' => ['rgb' => '4472c4']
					],
					'borders' => [
						'outline' => [
							'style' => \PHPExcel_Style_Border::BORDER_THICK,
							'color' => array('rgb' => '000000'),
						],
					],
				]
			],
			'Verticals' => [
				'width' => 22,
				'styles' => [
					"font" => [
						"size" => 9,
						'color' => ['rgb' => 'FFFFFF'],
						"bold" => true
					],
					'alignment' => [
						'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					],
					'fill' => [
						'type' => \PHPExcel_Style_Fill::FILL_SOLID,
						'color' => ['rgb' => '4472c4']
					],
					'borders' => [
						'outline' => [
							'style' => \PHPExcel_Style_Border::BORDER_THICK,
							'color' => array('rgb' => '000000'),
						],
					],
				]
			],
			'Geographic region' => [
				'width' => 14,
				'styles' => [
					"font" => [
						"size" => 9,
						'color' => ['rgb' => 'FFFFFF'],
						"bold" => true
					],
					'alignment' => [
						'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					],
					'fill' => [
						'type' => \PHPExcel_Style_Fill::FILL_SOLID,
						'color' => ['rgb' => '4472c4']
					],
					'borders' => [
						'outline' => [
							'style' => \PHPExcel_Style_Border::BORDER_THICK,
							'color' => array('rgb' => '000000'),
						],
					],
				]
			],
			'Country' => [
				'width' => 12,
				'styles' => [
					"font" => [
						"size" => 9,
						'color' => ['rgb' => 'FFFFFF'],
						"bold" => true
					],
					'alignment' => [
						'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					],
					'fill' => [
						'type' => \PHPExcel_Style_Fill::FILL_SOLID,
						'color' => ['rgb' => '4472c4']
					],
					'borders' => [
						'outline' => [
							'style' => \PHPExcel_Style_Border::BORDER_THICK,
							'color' => array('rgb' => '000000'),
						],
					],
				]
			],
			'Quantity Q1' => [
				'width' => 13,
				'styles' => [
					"font" => [
						"size" => 9,
						'color' => ['rgb' => 'FFFFFF'],
						"bold" => true
					],
					'alignment' => [
						'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					],
					'fill' => [
						'type' => \PHPExcel_Style_Fill::FILL_SOLID,
						'color' => ['rgb' => '92d050']
					],
					'borders' => [
						'outline' => [
							'style' => \PHPExcel_Style_Border::BORDER_THICK,
							'color' => array('rgb' => '000000'),
						],
					],
				]
			],
			'Quantity Q2' => [
				'width' => 13,
				'styles' => [
					"font" => [
						"size" => 9,
						'color' => ['rgb' => 'FFFFFF'],
						"bold" => true
					],
					'alignment' => [
						'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					],
					'fill' => [
						'type' => \PHPExcel_Style_Fill::FILL_SOLID,
						'color' => ['rgb' => '92d050']
					],
					'borders' => [
						'outline' => [
							'style' => \PHPExcel_Style_Border::BORDER_THICK,
							'color' => array('rgb' => '000000'),
						],
					],
				]
			],
			'Quantity Q3' => [
				'width' => 13,
				'styles' => [
					"font" => [
						"size" => 9,
						'color' => ['rgb' => 'FFFFFF'],
						"bold" => true
					],
					'alignment' => [
						'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					],
					'fill' => [
						'type' => \PHPExcel_Style_Fill::FILL_SOLID,
						'color' => ['rgb' => '92d050']
					],
					'borders' => [
						'outline' => [
							'style' => \PHPExcel_Style_Border::BORDER_THICK,
							'color' => array('rgb' => '000000'),
						],
					],
				]
			],
			'Quantity Q4' => [
				'width' => 13,
				'styles' => [
					"font" => [
						"size" => 9,
						'color' => ['rgb' => 'FFFFFF'],
						"bold" => true
					],
					'alignment' => [
						'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					],
					'fill' => [
						'type' => \PHPExcel_Style_Fill::FILL_SOLID,
						'color' => ['rgb' => '92d050']
					],
					'borders' => [
						'outline' => [
							'style' => \PHPExcel_Style_Border::BORDER_THICK,
							'color' => array('rgb' => '000000'),
						],
					],
				]
			],
			'Total Quantity 2017' => [
				'width' => 17,
				'styles' => [
					"font" => [
						"size" => 9,
						'color' => ['rgb' => 'FFFFFF'],
						"bold" => true
					],
					'alignment' => [
						'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					],
					'fill' => [
						'type' => \PHPExcel_Style_Fill::FILL_SOLID,
						'color' => ['rgb' => '92d050']
					],
					'borders' => [
						'outline' => [
							'style' => \PHPExcel_Style_Border::BORDER_THICK,
							'color' => array('rgb' => '000000'),
						],
					],
				]
			],
			'unit sale Price $' => [
				'width' => 13,
				'styles' => [
					"font" => [
						"size" => 9,
						'color' => ['rgb' => 'FFFFFF'],
						"bold" => true
					],
					'alignment' => [
						'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					],
					'fill' => [
						'type' => \PHPExcel_Style_Fill::FILL_SOLID,
						'color' => ['rgb' => '4472c4']
					],
					'borders' => [
						'outline' => [
							'style' => \PHPExcel_Style_Border::BORDER_THICK,
							'color' => array('rgb' => '000000'),
						],
					],
				]
			],
			'Probability in %' => [
				'width' => 13,
				'styles' => [
					"font" => [
						"size" => 9,
						'color' => ['rgb' => 'FFFFFF'],
						"bold" => true
					],
					'alignment' => [
						'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					],
					'fill' => [
						'type' => \PHPExcel_Style_Fill::FILL_SOLID,
						'color' => ['rgb' => '4472c4']
					],
					'borders' => [
						'outline' => [
							'style' => \PHPExcel_Style_Border::BORDER_MEDIUM,
							'color' => array('rgb' => '000000'),
						],
					],
				]
			]
		];
	}

	public function rules() {
		return [
			'Opp #' => [
				'value' => self::$auto_increment++
			],
			'Company' => [
				'value' => function ($model) {
					if (!isset($model['POTENTIALID'])) {
						return 'no data';
					} else {
						return $this->getModuleKey('Potentials', $model['POTENTIALID'], 'Subsidiary');
					}
				}
			],
			'Sales person' => [
				'value' => function ($model) {
					if (!isset($model['POTENTIALID'])) {
						return 'no data';
					} else {
						return $this->getModuleKey('Potentials', $model['POTENTIALID'], 'Potential Owner');
					}
				}
			],
			'Customer Name' => [
				'value' => function ($model) {
					if (!isset($model['POTENTIALID'])) {
						return 'no data';
					} else {
						return $this->getModuleKey('Potentials', $model['POTENTIALID'], 'Account Name');
					}
				}
			],
			'Product' => [
				'value' => function ($model) {
					return $model['Product'];
				}
			],
			'Segment' => [
				'value' => function ($model) {
					if (!isset($model['POTENTIALID'])) {
						return 'no data';
					} else {
						return $this->getModuleKey('Potentials', $model['POTENTIALID'], 'Segment');
					}
				}
			],
			'Business Line' => [
				'value' => function ($model) {
					if (!isset($model['POTENTIALID'])) {
						return 'not defined';
					} else {
						return $this->getModuleKey('Potentials', $model['POTENTIALID'], 'Business Line');
					}
				}
			],
			'Verticals' => [
				'value' => function ($model) {
					if (!isset($model['POTENTIALID'])) {
						return 'no data';
					} else {
						return $this->getModuleKey('Potentials', $model['POTENTIALID'], 'Vertical');
					}
				}
			],
			'Geographic region' => [
				'value' => function ($model) {
					if (!isset($model['POTENTIALID'])) {
						return 'no data';
					} else {
						return $this->getModuleKey('Potentials', $model['POTENTIALID'], 'Region');
					}
				}
			],
			'Country' => [
				'value' => function ($model) {
					if (!isset($model['POTENTIALID'])) {
						return 'no data';
					} else {
						if ($country = $this->getModuleKey('Potentials', $model['POTENTIALID'], 'Country.')) {
							return $country . ".";
						} else {
							return '';
						}
					}
				}
			],
			'Quantity Q1' => [
				'value' => function ($model) {
					return $model['Quantity Q1'] ?? 0;
				}
			],
			'Quantity Q2' => [
				'value' => function ($model) {
					return $model['Quantity Q2'] ?? 0;
				}
			],
			'Quantity Q3' => [
				'value' => function ($model) {
					return $model['Quantity Q3'] ?? 0;
				}
			],
			'Quantity Q4' => [
				'value' => function ($model) {
					return $model['Quantity Q4'] ?? 0;
				}
			],
			'Total Quantity 2017' => [
				'value' => function ($model) {
					return $model['Total Quantity 2017'] ?? 0;
				}
			],
			'unit sale Price $' => [
				'value' => function ($model) {
					return $model['unit sale Price $'];
					//'Unit Price'
				}
			],
			'Probability in %' => [
				'value' => function ($model) {
					return $model['Probability in %'];
				}
			],
		];
	}

	public function setModule($module, $data) {
		$this->modules[$module] = $data;
	}

	public function setModules($modules) {
		foreach ($modules as $module => $data) {
			$this->setModule($module, $data);
		}
	}

	public function getMapped($iterable) {
		$mapped = [];
		foreach ($this->rules() as $rule_name => $rule) {
			$label = $rule_name;
			if (isset($rule['label'])) $label = $rule['label'];
			$mapped[$label] = $this->processRule($rule, $iterable);
		}
		return $mapped;
	}

	private function processRule($rule, $iterable) {
		$value = isset($rule['value']) ? $rule['value'] : false;
		if ($value !== false && is_callable($value)) {
			return call_user_func($value->bindTo($this), $iterable);
		} elseif ($value !== false && !is_callable($value)) {
			return $value;
		}
	}

	public function getModule($module_name, $module_primary = false) {
		if (isset($this->modules[$module_name])) {
			if ($module_primary) {
				if (isset($this->modules[$module_name][$module_primary])) {
					return $this->modules[$module_name][$module_primary];
				} else {
					return null;
				}
			}
			return $this->modules[$module_name];
		} else {
			return null;
		}
	}

	public function getModuleKey($module_name, $module_primary, $key) {
		if (isset($this->modules[$module_name][$module_primary][$key])) {
			return $this->modules[$module_name][$module_primary][$key];
		} else {
			return null;
		}
	}
}