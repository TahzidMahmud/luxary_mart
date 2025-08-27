import React, { useRef } from "react";
import DatePicker from "./DatePicker";

interface SearchFormProps {
  fromDate: string;
  toDate: string;
  orderStatus: string;
  search:string;
  paymentStatus:string;
  onChange: (key: string, value: string) => void;
  onSearch: (e) => void;
  onReset: () => void;
  fromRef: React.RefObject<HTMLInputElement>; // new prop
}

const SearchForm: React.FC<SearchFormProps> = ({
  fromDate,
  toDate,
  search,
  paymentStatus,
  orderStatus,
  onChange,
  onSearch,
  onReset,
  fromRef
}) => {

  return (
    <form onSubmit={onSearch} className="mb-4 space-y-4 sm:space-y-0 sm:flex sm:items-end sm:space-x-4">

      <div className="flex flex-col grow">
        <DatePicker
            label="From"
            id="from"
            name="from"
            value={fromDate}
            ref={fromRef}
            onChange={(e) => {
                onChange("from", e.target.value)
            }}
        />
      </div>
      <div className="flex flex-col w-64">
        <label htmlFor="order_status" className="text-sm font-medium text-gray-700">Order Status</label>
        <select
          id="order_status"
          name="order_status"
          value={orderStatus}
          onChange={(e) => onChange("order_status", e.target.value)}
          className="rounded border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm h-10"
        >
          <option value="">All</option>
          <option value="pending">Pending</option>
          <option value="processing">Processing</option>
          <option value="completed">Completed</option>
          <option value="cancelled">Cancelled</option>
        </select>
      </div>
      <div className="flex flex-col w-64">
        <label htmlFor="payment_status" className="text-sm font-medium text-gray-700">Payment Status</label>
        <select
          id="payment_status"
          name="payment_status"
          value={paymentStatus}
          onChange={(e) => onChange("payment_status", e.target.value)}
          className="rounded border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm h-10"
        >
          <option value="">All</option>
          <option value="paid">Paid</option>
          <option value="unpaid">Unpaid</option>
        </select>
      </div>
      <div className="search-form relative flex-grow w-full">
            <input type="text" id="search" name="search"
                value={search} className="theme-input" placeholder="Search..."
                onChange={(e) => onChange("payment_status", e.target.value)}
                autoComplete="off" />
            <span
                className="text-primary dark:text-muted absolute top-0 right-0 h-full flex items-center justify-center px-3 pointer-events-none">
                <i className="fa-solid fa-magnifying-glass"></i>
            </span>
        </div>
      <div className="flex space-x-2">
        <button
          type="button"
          className="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700"
          onClick={onSearch}
        >
          Search
        </button>
        <button
          type="button"
          onClick={onReset}
          className="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400"
        >
          Reset
        </button>
      </div>
    </form>
  );
};

export default SearchForm;
