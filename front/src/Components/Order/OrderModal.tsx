import React from 'react';
import Style from '../../Styles/Order/MyOrder.module.css';
import {Order} from "../../Pages/Order/MyOrders";
import Button from "@mui/material/Button";
import IconButton from "@mui/material/IconButton";
import CloseIcon from "@mui/icons-material/Close";

type OrderModalProps = {
    order: Order,
    setModalOpen: (value: boolean) => void,
}

export default function OrderModal(props: OrderModalProps): React.JSX.Element {

    const {order, setModalOpen} = props;
    const date = new Date(order.creationDate);
    return (
        <div className={Style.modalContainer}>
            <div className={Style.modalCard}>
                <div className={Style.closeBtnDiv} onClick={() => setModalOpen(false)}>
                    <CloseIcon className={Style.closeBtn}/>
                </div>
                <div className={Style.modalCardHeader}>
                    <h2 className={Style.modalCardHeaderTitle}>HERMHESS</h2>
                </div>
                <div className={Style.modalCardBody}>
                    <div className={Style.modalCardBodyDetails}>
                        <p>Commande No : #{order.id} :</p>
                        <p>Date : {date.toLocaleDateString("fr-FR", {
                            day: '2-digit',
                            month: '2-digit',
                            year: 'numeric'
                        })}
                        </p>
                    </div>
                    <div className={Style.modalCardBodyContent}>
                        {order.products && order.products.map(product => (
                            <div className={Style.modalProduct}>
                                <img className={Style.modalProductImage} src={product.photo} alt={product.name}/>
                                <div className={Style.modalProductInfo}>
                                    <div className={Style.modalProductName}>
                                        <p>{product.name}</p>
                                        <p>+ {product.price} &euro;</p>
                                    </div>
                                    <div className={Style.modalProductQty}>
                                        <p>Quantité : 1</p>
                                    </div>
                                </div>
                            </div>
                        ))
                        }
                    </div>
                    <div className={Style.modalCardBodyTotal}>
                        <p>Total :</p>
                        <p>{order.totalPrice} &euro;</p>
                    </div>
                </div>
            </div>
        </div>
    );
}