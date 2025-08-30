// src/pos/popup/ProductPicturesModal.tsx
import React, { useState } from "react";
import { LiaTimesSolid } from "react-icons/lia";
import ModalWrapper from "./ModalWrapper";
import Image from "../../react/components/Image";
import { IPosProductVariation } from "../types";
import { SideBySideMagnifier } from 'react-image-magnifiers';

interface Props {
  product: IPosProductVariation;
}

const ProductPicturesModal = ({ product }: Props) => {
  const [isActive, setIsActive] = useState(false);

  const handleClose = () => setIsActive(false);

  return (
    <>
      <button
        className="py-2 flex items-center justify-center gap-2 border-t border-border hover:text-white hover:bg-theme-primary w-full"
        onClick={() => setIsActive(true)}
      >
        Real Picture
      </button>

      <ModalWrapper isActive={isActive} onClose={handleClose} size="xl">
        <div className="theme-modal__header">
          <h5>{product.name} - Real Pictures</h5>
          <button
            className="text-xl text-white sm:text-theme-secondary-light"
            type="button"
            onClick={handleClose}
          >
            <LiaTimesSolid />
          </button>
        </div>

        <div className="theme-modal__body grid grid-cols-2 gap-1 ">
          {product.image ? (
                <SideBySideMagnifier
                    imageSrc={product.image}
                    className="[&_img]:max-w-none"
                    alwaysInPlace={true}
                />
            )
           : (
            <p>No pictures available.</p>
          )}
        </div>
      </ModalWrapper>
    </>
  );
};

export default ProductPicturesModal;
