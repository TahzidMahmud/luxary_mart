import React from "react";
import { LiaTimesSolid } from "react-icons/lia";
import ModalWrapper from "./ModalWrapper";
import { ICustomer } from "../types";

interface Props {
  customer: ICustomer| null;
  isActive: boolean;
  onClose: () => void;
}

const CustomerDetailsModal = ({ customer, isActive, onClose }: Props) => {
  return (
    <ModalWrapper isActive={isActive} onClose={onClose}>
      <div className="theme-modal__header">
        <h5>Customer Details</h5>
        <button
          className="text-xl text-white sm:text-theme-secondary-light"
          type="button"
          onClick={onClose}
        >
          <LiaTimesSolid />
        </button>
      </div>
    {customer && (
        <div className="theme-modal__body space-y-3">
                <p><strong>Name:</strong> {customer.name}</p>
                <p><strong>Phone:</strong> {customer.phone}</p>
            {customer.addresses?.length > 0 && (
                    <p><strong>Address:</strong> {customer.addresses[0].address}</p>
                )}
                {customer.addresses?.length > 0 && (
                    <p><strong>Direction:</strong> {customer.addresses[0].direction}</p>
                )}
        </div>
    )}

    </ModalWrapper>
  );
};

export default CustomerDetailsModal;
