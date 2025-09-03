import React, { useRef } from 'react';
import { FaCaretDown } from 'react-icons/fa';
import Select, { Props as SelectProps } from 'react-select';
import { cn } from '../../utils/cn';
import { translate } from '../../utils/translate';

interface Props<T> extends SelectProps<T> {
    options: T[];
    label?: string;
    className?: string;
    groupClassName?: string;
    error?: string;
    touched?: boolean;
    statusColorClass?: string;
    onNoMatch?: (inputValue: string) => void;
    onFullMatch?: (matchedOption: T) => void;
}

const SelectInput = ({
    options = [],
    label,
    classNames,
    groupClassName,
    placeholder,
    error,
    touched,
     statusColorClass,
    ...rest
}: Props<any>) => {
    const hasError = error && touched;
 const lastInputRef = useRef("");
    const selected = options.find((opt) => {
        if (rest.getOptionValue) {
            return rest.getOptionValue(opt) == rest.value;
        }

        return opt.value == rest.value;
    });

    return (
        <div className={groupClassName}>
            {label && <label>{label}</label>}
            <div className={`rounded px-2 py-1 text-white`}>
                <Select
                    {...rest}
                    isSearchable
                    value={selected || null}
                    options={rest.isLoading ? [] : options}
                    placeholder={placeholder || translate('Select here')}
                    onInputChange={(inputValue,actionMeta) => {
                        lastInputRef.current = inputValue;
                       if (rest.onInputChange) {
                        rest.onInputChange(inputValue, actionMeta);
                        }
                    }}
                    onBlur={(e) => {
                        if (rest.onBlur) rest.onBlur(e);
                        const inputValue = lastInputRef.current.trim().toLowerCase();
                        if (!inputValue) return;

                        const exactMatch = options.find((opt) => {
                        if (rest.getOptionLabel) {
                            const label = rest.getOptionLabel(opt).toLowerCase(); // "name(phone)"
                            const name = (opt.name || "").toLowerCase();
                            const phone = (opt.phone || "").toLowerCase();

                            return (
                            label === inputValue ||     // full "name(phone)" match
                            name === inputValue ||      // name-only match
                            phone === inputValue        // phone-only match
                            );
                        }
                        return false;
                        });
                        if (exactMatch) {
                            rest.onFullMatch?.(exactMatch);
                            return;
                        }
                        const hasMatch = options.some((opt) => {
                        if (rest.getOptionLabel) {
                            return rest
                            .getOptionLabel(opt)
                            .toLowerCase()
                            .includes(inputValue.toLowerCase());
                        }
                        return false;
                        });

                        if (!hasMatch && inputValue.trim() !== "") {
                        rest.onNoMatch?.(inputValue); // 👈 fire event to parent
                        }
                    }}
                      onKeyDown={(e) => {
                        if (e.key === "Enter") {
                        const inputValue = (e.currentTarget as HTMLInputElement).value;
                        const hasMatch = options.some((opt) => {
                            if (rest.getOptionLabel) {
                            return rest
                                .getOptionLabel(opt)
                                .toLowerCase()
                                .includes(inputValue.toLowerCase());
                            }
                            return false;
                        });

                        if (!hasMatch && inputValue.trim() !== "") {
                            e.preventDefault();
                            rest.onNoMatch?.(inputValue); // 👈 fire event to parent
                        }
                        }
                    }}
                    components={{
                        IndicatorSeparator: () => null,
                        DropdownIndicator: () => (
                            <span className="text-secondary-light text-sm">
                                <FaCaretDown />
                            </span>
                        ),
                    }}
                    className=""
                    classNames={{
                        ...classNames,
                        placeholder: (state) =>
                            cn(
                                `!text-[13px] !text-muted`,
                                classNames?.placeholder?.(state),
                            ),
                        control: (state) =>
                            cn(
                                statusColorClass?`${statusColorClass} rounded px-3 py-1 !min-h-[37px] min-w-[100px]`: `rounded px-3 py-1 !min-h-[37px] min-w-[100px] !bg-background !border !border-border`,
                                classNames?.control?.(state),
                            ),
                        menu: (state) =>
                            statusColorClass?`${statusColorClass}`:`!z-[2] !bg-background ${classNames?.menu?.(state)}`,
                        indicatorsContainer: (state) =>
                            `!text-secondary-light pl-2 [&>*]:!p-0 ${classNames?.indicatorsContainer?.(
                                state,
                            )}`,
                        singleValue: (state) =>
                            `text-sm ${statusColorClass?'!text-white':'text-white'} rounded px-2 py-1 ${classNames?.singleValue?.(
                                state,
                            )}`,
                        indicatorSeparator: (state) =>
                            `!hidden ${classNames?.indicatorSeparator?.(state)}`,
                        valueContainer: (state) =>
                            `!p-0 ${classNames?.valueContainer?.(state)}`,
                        input: (state) =>
                            `!p-0 !m-0 text-sm !text-foreground ${classNames?.input?.(
                                state,
                            )}`,
                        option: (state) =>
                            `!py-1 !text-foreground ${
                                state.isSelected
                                    ? '!bg-secondary-light'
                                    : state.isFocused && '!bg-secondary-light/30'
                            }`,

                    }}
                />
            </div>

            {hasError && (
                <span className="text-sm text-theme-alert">{error}</span>
            )}
        </div>
    );
};

export default SelectInput;
