<?php

namespace PhpSchool\CliMenu\MenuItem;

use PhpSchool\CliMenu\MenuStyle;
use PhpSchool\CliMenu\Util\StringUtil;
use PhpSchool\CliMenu\Style\SelectableStyle;

/**
 * @author Michael Woodward <mikeymike.mw@gmail.com>
 */
class SelectableItem implements MenuItemInterface
{
    /**
     * @var string
     */
    private $text;

    /**
     * @var callable
     */
    private $selectAction;

    /**
     * @var bool
     */
    private $showItemExtra;

    /**
     * @var bool
     */
    private $disabled;

    /**
     * @var SelectableStyle
     */
    private $style;

    public function __construct(
        string $text,
        callable $selectAction,
        bool $showItemExtra = false,
        bool $disabled = false
    ) {
        $this->text = $text;
        $this->selectAction = $selectAction;
        $this->showItemExtra = $showItemExtra;
        $this->disabled = $disabled;

        $this->style = new SelectableStyle();
    }

    /**
     * The output text for the item
     */
    public function getRows(MenuStyle $style, bool $selected = false) : array
    {
        $marker = sprintf("%s", $this->style->getMarker($selected));

        $length = $this->style->getDisplaysExtra()
            ? $style->getContentWidth() - (mb_strlen($this->style->getItemExtra()) + 2)
            : $style->getContentWidth();

        $rows = explode(
            "\n",
            StringUtil::wordwrap(
                sprintf('%s%s', $marker, $this->text),
                $length,
                sprintf("\n%s", str_repeat(' ', mb_strlen($marker)))
            )
        );

        return array_map(function ($row, $key) use ($style, $length) {
            $text = $this->disabled ? $style->getDisabledItemText($row) : $row;

            if ($key === 0) {
                return $this->showItemExtra
                    ? sprintf(
                        '%s%s  %s',
                        $text,
                        str_repeat(' ', $length - mb_strlen($row)),
                        $this->style->getItemExtra()
                    )
                    : $text;
            }

            return $text;
        }, $rows, array_keys($rows));
    }

    /**
     * Return the raw string of text
     */
    public function getText() : string
    {
        return $this->text;
    }

    /**
     * Set the raw string of text
     */
    public function setText(string $text) : void
    {
        $this->text = $text;
    }

    /**
     * Execute the items callable if required
     */
    public function getSelectAction() : ?callable
    {
        return $this->selectAction;
    }

    /**
     * Can the item be selected
     */
    public function canSelect() : bool
    {
        return !$this->disabled;
    }

    /**
     * Whether or not we are showing item extra
     */
    public function showsItemExtra() : bool
    {
        return $this->showItemExtra;
    }

    /**
     * Enable showing item extra
     */
    public function showItemExtra() : void
    {
        $this->showItemExtra = true;
    }

    /**
     * Disable showing item extra
     */
    public function hideItemExtra() : void
    {
        $this->showItemExtra = false;
    }

    public function getStyle() : SelectableStyle
    {
        return $this->style;
    }

    public function setStyle(SelectableStyle $style) : self
    {
        $this->style = $style;

        return $this;
    }
}
