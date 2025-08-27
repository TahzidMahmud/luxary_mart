import React, { useEffect, useState } from 'react';
import { useAppSelector } from '../../../../order/hooks';
import SelectInput from '../../inputs/SelectInput';
import { PathaoCity } from '../../../types';
import { useTrackCourier } from '../../../../api/courierApi';

interface CourierOrderModalProps {
  isOpen: boolean;
  onClose: () => void;
  partner: 'pathao' | 'steadfast' | 'redx' | '';
  trackingId:number;
  onSubmit: (data: Record<string, any>) => void;
}

const CourierDetailsModal: React.FC<CourierOrderModalProps> = ({
  isOpen,
  onClose,
  partner,
  trackingId,
  onSubmit,
}) => {
const [loading,setLoading]=useState(false);
  // Shared fields
const {data} = useTrackCourier(setLoading,trackingId,partner);
  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50">
      <div className="bg-[#1c1c1e] p-6 rounded-xl shadow-xl w-full max-w-2xl">
        <h2 className="text-lg font-semibold text-white mb-4 capitalize">
          Placed Order Status - {partner}
        </h2>

        <form onSubmit={onSubmit}>
          {/* Conditionally Rendered Fields */}
          {loading?(
            <div className="text-lg font-bold flex items-center">
               <p className="text-red-500"> Loading Status ....!</p>
            </div>):(
            <pre>{JSON.stringify(data, null, 2)}</pre>
          )}

          {/* Buttons */}
          <div className="flex justify-end gap-2 mt-6">
            <button
              type="button"
              onClick={onClose}
              className="px-4 py-2 rounded-lg bg-gray-600 text-white hover:bg-gray-500 transition"
            >
              Cancel
            </button>

          </div>
        </form>
      </div>
    </div>
  );
};

export default CourierDetailsModal;
