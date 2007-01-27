<?php

echo $this->section->getType() . ZF_ENVIRONMENT_EOL . str_repeat(ZF_ENVIRONMENT_UNDERLINE, ZF_ENVIRONMENT_WIDTH) . ZF_ENVIRONMENT_EOL;

foreach ($this->section as $field) {

    echo str_repeat(' ', ZF_ENVIRONMENT_INSET) . sprintf("%10s %-" . ZF_ENVIRONMENT_COLWIDTH . "s", 'Name:', $field->name) . ZF_ENVIRONMENT_EOL;
    echo str_repeat(' ', ZF_ENVIRONMENT_INSET) . sprintf("%10s %-" . ZF_ENVIRONMENT_COLWIDTH . "s", 'Title:', $field->title) . ZF_ENVIRONMENT_EOL;
    echo str_repeat(' ', ZF_ENVIRONMENT_INSET) . sprintf("%10s %-" . ZF_ENVIRONMENT_COLWIDTH . "s", 'Value:', $field->value) . ZF_ENVIRONMENT_EOL;
    echo str_repeat(' ', ZF_ENVIRONMENT_INSET) . sprintf("%10s %-" . ZF_ENVIRONMENT_COLWIDTH . "s", 'Version:', $field->version) . ZF_ENVIRONMENT_EOL;
    echo str_repeat(' ', ZF_ENVIRONMENT_INSET) . sprintf("%10s %-" . ZF_ENVIRONMENT_COLWIDTH . "s", 'Info:', $field->info) . ZF_ENVIRONMENT_EOL;

    if ($field->info && (is_array($field->info) || ($field->info instanceof Iterator))) {
        foreach ($field->info as $key => $value) {
            $indent = strlen($key) + 4;

            if (is_array($value)) {
                $values = array();

                foreach ($value as $index => $property) {
                    $values[] = $index . ZF_ENVIRONMENT_DELIMITER . $property;
                }

                $value = join(ZF_ENVIRONMENT_EOL . str_repeat(' ', ZF_ENVIRONMENT_INDENT + ZF_ENVIRONMENT_PADDING), $values);
            }

            $content = wordwrap($value, ZF_ENVIRONMENT_COLWIDTH - ZF_ENVIRONMENT_INDENT, ZF_ENVIRONMENT_EOL . str_repeat(' ', ZF_ENVIRONMENT_PADDING + ZF_ENVIRONMENT_INDENT));
            echo str_repeat(' ', ZF_ENVIRONMENT_PADDING) . $key
                                           . ZF_ENVIRONMENT_DELIMITER
                                           . $content
                                           . ZF_ENVIRONMENT_EOL;
        }
    }

    echo ZF_ENVIRONMENT_EOL;
}
