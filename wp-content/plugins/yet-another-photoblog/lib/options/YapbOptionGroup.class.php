<?php
	
	/**
	 * This class acts as a collector for either 
	 * options or other groups
	 **/

	if (!class_exists('YapbOptionGroup')) {

		class YapbOptionGroup {

			var $title;
			var $description;
			var $children;
			var $level;
			var $isPlugin;

			function YapbOptionGroup($title, $description, $children=array(), $level=0) {

				$this->title = $title;
				$this->description = $description;
				$this->children = $children;
				$this->setLevel($level);
				$this->isPlugin = false;

			}

			function setLevel($level) {
				$this->level = $level;
				for ($i=0, $ilen=count($this->children); $i<$ilen; $i++) {
					$child = &$this->children[$i];
					$child->setLevel($level+1);
				}
			}

			function initialize() {
				foreach ($this->children as $child) {
					$child->initialize();
				}
			}

			function update() {
				foreach ($this->children as $child) {
					$child->update();
				}
			}

			function add($optionOrGroup) {
				$this->children[] = $optionOrGroup;
			}

			function toString() {

				$result = '';

				switch ($this->level) {

					default:
					case 0:

						// Option Accordion on the page

						if (!empty($this->children)) {

							// Tabs UL

							$result .= '<a name="anchor" class="basic-accordion-anchor">&nbsp;</a>';
							$result .= '<div id="accordion" class="basic-accordion">';

							// Accordion Items

							for ($i=0, $ilen=count($this->children); $i<$ilen; $i++) {

								$child = &$this->children[$i];

								if ($child->isPlugin) $result .= '<div class="yapb-plugin">';

								$result .= '<a href="#" class="basic-accordion-link' . $additionalCSS . '">' . $child->title . '</a>';
								$result .= '<div class="basic-accordion-content">';
								$result .= $child->toString();
								$result .= '</div>';

								if ($child->isPlugin) $result .= '</div>';

							}

							$result .= '</div>';

						}

						break;

					case 1: 

						// Outermost WordPress Options Grouping

						// $result = '<h3>' . ' ' . $this->title . '</h3>';
						if (!empty($this->description)) $result .= '<p>' . $this->description . '</p>';

						if (!empty($this->children)) {

							$result .= '<table style="height:auto;" class="form-table">';
							for ($i=0, $ilen=count($this->children); $i<$ilen; $i++) {
								$child = &$this->children[$i];
								$result .= $child->toString();
							}
							$result .= '</table>';

						}



						break;

					case 2:

						// Inner WordPress Option Grouping

						$result .= '<tr>';
						$result .= '<th valign="top" align="left">' . ' ' . $this->title . '</th>';
						$result .= '<td valign="top">';

						if (!empty($this->description)) {
							$result .= "\n" . '<p class="yapb-first">' . $this->description . '</p>';
						}
						
						$result .= "\n" . '<ul class="yapb">';

						foreach ($this->children as $optionInstance) {
							$result .= "\n\n" . '<li>' . $optionInstance->toString() . '</li>';
						}

						$result .= "\n" . '</ul>';
						$result .= "\n" . '</td>';
						$result .= '</tr>';

						break;

				}

				return $result;

			}

		}

	}

?>