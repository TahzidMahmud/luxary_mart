import React, { forwardRef } from "react";

interface DatePickerProps {
  label: string;
  id: string;
  name: string;
  value?: string;
  onChange?: (e: React.ChangeEvent<HTMLInputElement>) => void;
}

const DatePicker = forwardRef<HTMLInputElement, DatePickerProps>(
  ({ label, id, name, value, onChange }, ref) => {
    return (
      <div className="flex items-center gap-2 w-64">
        <label htmlFor={id} className="text-sm font-medium text-gray-700">
          {label}
        </label>
        <div className="date-picker date-picker--range w-64">
          <input
            type="text"
            id={id}
            className="theme-input"
            name={name}
            defaultValue={value}
            ref={ref}
            onChange={onChange}
          />
        </div>
      </div>
    );
  }
);

export default DatePicker;
