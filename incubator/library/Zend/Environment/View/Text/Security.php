<?php

echo $this->section->getType() . ZF_ENVIRONMENT_EOL . str_repeat(ZF_ENVIRONMENT_UNDERLINE, ZF_ENVIRONMENT_WIDTH) . ZF_ENVIRONMENT_EOL;

foreach ($this->section as $field) {

    echo sprintf("%" . ZF_ENVIRONMENT_TITLEWIDTH . "s %-" . ZF_ENVIRONMENT_COLWIDTH . "s", 'Group:', $field->group) . ZF_ENVIRONMENT_EOL;
    echo sprintf("%" . ZF_ENVIRONMENT_TITLEWIDTH . "s %-" . ZF_ENVIRONMENT_COLWIDTH . "s", 'Name:', $field->name) . ZF_ENVIRONMENT_EOL;
    echo sprintf("%" . ZF_ENVIRONMENT_TITLEWIDTH . "s %-" . ZF_ENVIRONMENT_COLWIDTH . "s", 'Result:', $field->result) . ZF_ENVIRONMENT_EOL;
    echo sprintf("%" . ZF_ENVIRONMENT_TITLEWIDTH . "s %-" . ZF_ENVIRONMENT_COLWIDTH . "s", 'Current:', $field->current_value) . ZF_ENVIRONMENT_EOL;
    echo sprintf("%" . ZF_ENVIRONMENT_TITLEWIDTH . "s %-" . ZF_ENVIRONMENT_COLWIDTH . "s", 'Recommended:', $field->recommended_value) . ZF_ENVIRONMENT_EOL;
    echo sprintf("%" . ZF_ENVIRONMENT_TITLEWIDTH . "s %-" . ZF_ENVIRONMENT_COLWIDTH . "s", 'More Info:', $field->link) . ZF_ENVIRONMENT_EOL;
    echo sprintf("%" . ZF_ENVIRONMENT_TITLEWIDTH . "s ", 'Details:') . wordwrap($field->details, ZF_ENVIRONMENT_COLWIDTH - ZF_ENVIRONMENT_INDENT, ZF_ENVIRONMENT_EOL . str_repeat(' ', ZF_ENVIRONMENT_PADDING + ZF_ENVIRONMENT_INDENT)) . ZF_ENVIRONMENT_EOL;
    echo ZF_ENVIRONMENT_EOL;
}
