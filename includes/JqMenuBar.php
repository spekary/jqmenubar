<?php
/**
 * A menu bar control
 */

class JqMenuBar_SelectEvent extends QJqUiEvent {
    const EventName = 'menubarselect';
    const JsReturnParam = 'ui.item[0].id';
}


class JqMenuBar extends QHtmlList {
    protected $strSelectedId = null;

    public function __construct($objParentObject, $strControlId = null) {
        parent::__construct ($objParentObject, $strControlId);
		$this->registerFiles();
		$this->SetCustomStyle ('visibility', 'hidden'); // hide while menu bar is being activated so ugly html list won't be flashed.
	}

	protected function registerFiles() {
		$this->AddCssFile(__JQUERY_CSS__); // make sure they know 
		$this->AddPluginJavascriptFile("jqmenubar", "jquery.ui.menubar.js");
		$this->AddPluginCssFile("jqmenubar", "jquery.menubar.css");
	}

    public function AddMenuItem ($objMenuItem) {
        parent::AddListItem ($objMenuItem);
    }

    protected function makeJsProperty($strProp, $strKey) {
        $objValue = $this->$strProp;
        if (null === $objValue) {
            return '';
        }

        return $strKey . ': ' . JavaScriptHelper::toJsObject($objValue) . ', ';
    }

    protected function makeJqOptions() {
        $strJqOptions = '';
        if ($strJqOptions) $strJqOptions = substr($strJqOptions, 0, -2);
        return $strJqOptions;
    }

    public function getJqSetupFunction() {
        return 'menubar';
    }

    public function GetControlJavaScript() {
        $strJs = sprintf('jQuery("#%s").%s({%s})', $this->getJqControlId(), $this->getJqSetupFunction(), $this->makeJqOptions());
        $strJs .= <<<JS
            .on("menubarselect", function( event, ui ) {
                qcubed.recordControlModification(this.id, "SelectedId", ui.item[0].id);
            })
            .css ('visibility', 'visible');
JS;
        return $strJs;

    }

    public function GetEndScript() {
        $str = '';
        if ($this->getJqControlId() !== $this->ControlId) {
            // #845: if the element receiving the jQuery UI events is different than this control
            // we need to clean-up the previously attached event handlers, so that they are not duplicated
            // during the next ajax update which replaces this control.
            $str = sprintf('jQuery("#%s").off(); ', $this->getJqControlId());
        }
        return $str . $this->GetControlJavaScript() . '; ' . parent::GetEndScript();
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

class JqMenuItem extends QHtmlListItem {
    protected $strIcon = null;
    protected $strData;
    protected $blnIsDivider = false;
    protected $blnDisabled = false;

    public function __construct($strText, $strAnchor = null, $strData = null) {
        parent::__construct ($strText, $strAnchor);
        if ($strText == '-') {
            $this->blnIsDivider = true;
        }
        $this->strData = $strData;
    }

    public function AddMenuItem ($objMenuItem) {
        parent::AddListItem ($objMenuItem);
    }

    public function GetAttributes() {
        $strHtml = parent::GetAttributes();
        if ($this->blnDisabled) {
            $strHtml .= ' class="ui-state-disabled"';
        }
        return $strHtml;
    }

    public function GetInnerHtml() {
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
            case "Data": return $this->strData;

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
            case "Data":
                try {
                    $this->strData = QType::Cast($mixValue, QType::String);
                    break;
                } catch (QInvalidCastException $objExc) {
                    $objExc->IncrementOffset();
                    throw $objExc;
                }
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