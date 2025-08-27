import React, { useEffect, useState } from 'react';
import { FiTruck } from 'react-icons/fi';
import { LiaTimesSolid } from 'react-icons/lia';
import Button from '../../../react/components/inputs/Button';
import { cn } from '../../../react/utils/cn';
import { translate } from '../../../react/utils/translate';
import { IAddress } from '../../types';
import { getAddresses } from '../../utils/actions';
import ModalWrapper from '../ModalWrapper';
import AddressForm from './AddressForm';

interface Props {
    customerId: number | undefined;
    selectedAddressId?: number;
    onSuccess?: (address: IAddress) => void;
}

const AddressModal = ({ selectedAddressId, customerId, onSuccess }: Props) => {
    const [isActive, setIsActive] = useState(false);
    const [isAddAddressFormActive, setIsAddAddressFormActive] = useState(false);

    const [addresses, setAddresses] = useState<IAddress[]>([]);

    useEffect(() => {
        if (!customerId) return;

        getAddresses(customerId).then((data) => {
            setAddresses(data);
        });
    }, [customerId]);

    const handleClose = () => {
        setIsActive(false);
        setIsAddAddressFormActive(false);
    };

    const handleAddressAdded = (address: IAddress) => {
        onSuccess?.(address);
        setIsActive(false);
        setIsAddAddressFormActive(false);

        if (customerId) {
            getAddresses(customerId).then((data) => {
                setAddresses(data);
            });
        }
    };

    const handleSelectAddress = (address: IAddress) => {
        onSuccess?.(address);
        setIsActive(false);
    };

    return (
        <>
            <Button
                variant="primary"
                className="text-2xl h-[37px]"
                onClick={() => setIsActive(true)}
                disabled={!customerId}
            >
                <FiTruck />
            </Button>

            <ModalWrapper isActive={isActive} onClose={handleClose}>
                <div className="theme-modal__header">
                    <h5>{translate('Address')}</h5>

                    <button
                        className="text-xl text-white sm:text-theme-secondary-light"
                        onClick={handleClose}
                    >
                        <LiaTimesSolid />
                    </button>
                </div>

                <div className="theme-modal__body">
                    {!isAddAddressFormActive ? (
                        <div className="space-y-3">
                            {addresses.map((address) => (
                                <div
                                    key={address.id}
                                    className={cn(
                                        'px-5 py-4 border border-border rounded-md cursor-pointer hover:bg-background-hover',
                                        {
                                            'border-theme-primary bg-background-primary-light':
                                                selectedAddressId ===
                                                address.id,
                                        },
                                    )}
                                    onClick={() => handleSelectAddress(address)}
                                >
                                    <div>
                                        <h4 className="font-semibold mb-2">
                                            {address.area.name}
                                        </h4>
                                        <p>{address.fullAddress}</p>
                                    </div>
                                </div>
                            ))}

                            <button
                                className="px-5 py-4 border border-border rounded-md cursor-pointer bg-background-primary-light hover:bg-background-hover text-center w-full"
                                onClick={() => setIsAddAddressFormActive(true)}
                            >
                                Add New Address
                            </button>
                        </div>
                    ) : (
                        <AddressForm
                            customerId={customerId}
                            onSuccess={handleAddressAdded}
                        />
                    )}
                </div>
            </ModalWrapper>
        </>
    );
};

export default AddressModal;
