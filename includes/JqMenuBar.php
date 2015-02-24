<?php
/**
 * A menu bar control
 */

class JqMenuBar_SelectEvent extends QJqUiEvent {
    const EventName = 'menubarselect';
    const JsReturnParam = '{"id": ui.item[0].id, "value":$j(ui.item[0]).data("value")}';
}


class JqMenuBar extends QHListControl {
    protected $strSelectedId = null;

    public function __construct($objParentObject, $strControlId = null) {
        parent::__construct ($objParentObject, $strControlId);
		$this->registerFiles();
		$this->SetCssStyle('visibility', 'hidden'); // hide while menu bar is being activated so ugly html list won't be flashed.
	}

	protected function registerFiles() {
		$this->AddCssFile(__JQUERY_CSS__); // make sure they know 
		$this->AddPluginJavascriptFile("jqmenubar", "jquery.ui.menubar.js");
		$this->AddPluginCssFile("jqmenubar", "jquery.menubar.css");
	}

    public function AddMenuItem ($objMenuItem) {
        parent::AddItem ($objMenuItem);
    }


    public function getJqSetupFunction() {
        return 'menubar';
    }

	public function GetEndScript() {
		if ($this->getJqControlId() !== $this->ControlId) {
			// If events are not attached to the actual object being drawn, then the old events will not get
			// deleted. We delete the old events here. This code must happen before any other event processing code.
			QApplication::ExecuteControlCommand($this->getJqControlId(), "off", QJsPriority::High);
		}
		QApplication::ExecuteControlCommand($this->getJqControlId(), $this->getJqSetupFunction());
		QApplication::ExecuteControlCommand($this->getJqControlId(), 'on', "menubarselect", new QJsClosure('qcubed.recordControlModification(this.id, "SelectedId", ui.item[0].id)', ['event', 'ui']));
		QApplication::ExecuteControlCommand($this->getJqControlId(), 'css', 'visibility', 'visible');

		return parent::GetEndScript();
	}


	public function GetItemText (QHListItem $objItem) {
		if ($objItem->IsDivider) {
			return'-';
		}

		$strHtml = QApplication::HtmlEntities($objItem->Text);

		if ($strIcon = $objItem->Icon) {
			$strHtml = QHtml::RenderTag ('span', ['class'=>'ui-icon ui-icon-' . $strIcon], null, false, true) . $strHtml;
		}

		if ($strAnchor = $objItem->Anchor) {
			$strHtml = QHtml::RenderTag('a', ['href' => $strAnchor], $strHtml, false, true);
		}
		return $strHtml;
	}

	public function GetItemStyler (QHListItem $objItem) {
		$objStyler = parent::GetItemStyler($objItem);
		if ($objItem->Disabled) {
			$objStyler->AddCssClass('ui-state-disabled');
		}
		return $objStyler;
	}

	public function __get($strName) {
        switch ($strName) {
            case "SelectedId": return $this->strSelectedId;

            default:
                try {
                    return parent::__get($strName);
                } catch (QCallerException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
        }
    }


    public function __set($strName, $mixValue) {
        switch ($strName) {
            case "SelectedId":
                try {
                    $this->strSelectedId = QType::Cast($mixValue, QType::String);
                    break;
                } catch (QInvalidCastException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }


            default:
                try {
                    parent::__set($strName, $mixValue);
                } catch (QCallerException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
                break;
        }

    }
}

class JqMenuItem extends QHListItem {
    protected $strIcon = null;
    protected $blnIsDivider = false;
    protected $blnDisabled = false;

    public function __construct($strText, $strValue = null, $strAnchor = null) {
        parent::__construct ($strText, $strValue, $strAnchor);
        if ($strText == '-') {
            $this->blnIsDivider = true;
        }
    }

    public function AddMenuItem ($objMenuItem) {
        parent::AddItem ($objMenuItem);
    }

    public function GetItemHtml($objItem) {
        if ($this->blnIsDivider) {
            return '-';
        } else {
            $strHtml = QApplication::HtmlEntities($this->strText);

            if ($this->strIcon) {
                $strHtml = '<span class="ui-icon ui-icon-' . $this->strIcon . '"></span>' . $strHtml;
            }

            if ($this->strAnchor) {
                $strHtml = sprintf ('<a href="%s">%s</a>', $this->strAnchor, $strHtml);
            }

            return $strHtml;
        }
    }

    public function __get($strName) {
        switch ($strName) {
            case "Icon": return $this->strIcon;
            case "IsDivider": return $this->blnIsDivider;
            case "Disabled": return $this->blnDisabled;

            default:
                try {
                    return parent::__get($strName);
                } catch (QCallerException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
        }
    }

    public function __set($strName, $mixValue) {
        switch ($strName) {
            case "Icon":
                try {
                    $this->strIcon = QType::Cast($mixValue, QType::String);
                    break;
                } catch (QInvalidCastException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }

            case "IsDivider":
                try {
                    $this->blnIsDivider = QType::Cast($mixValue, QType::Boolean);
                    break;
                } catch (QInvalidCastException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }

            case "Disabled":
                try {
                    $this->blnDisabled = QType::Cast($mixValue, QType::Boolean);
                    break;
                } catch (QInvalidCastException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }

            default:
                try {
                    parent::__set($strName, $mixValue);
                } catch (QCallerException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
                break;
        }
    }
}

class JqMenu extends JqMenuItem {

}