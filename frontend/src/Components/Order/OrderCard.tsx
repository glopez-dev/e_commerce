import React, {useState} from 'react';
import {Order} from "../../Pages/Order/MyOrders";
import Style from "../../Styles/Order/MyOrder.module.css";
import OrderModal from "./OrderModal";

export default function OrderCard(order: Order) {
    const [modalOpen, setModalOpen] = useState<boolean>(false);
    const date = new Date(order.creationDate);

    return (
        <>
            <div className={Style.card} onClick={(e) => setModalOpen(true)}>
                <div className={Style.cardTitle}>
                    <p>Commande #{order.id}</p>
                </div>
                <div className={Style.cardDate}>
                    <p>Le {date.toLocaleDateString("fr-FR", {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric'
                    }) + " à " + date.toLocaleTimeString("fr-FR", {
                        hourCycle: 'h24',
                        hour: '2-digit',
                        minute: '2-digit'
                    })} </p>
                </div>
                <div className={Style.cardBody}>
                    <div className={Style.recap}>
                        <p>
                            Total
                            {order.products.length > 1 ?
                                " de " + order.products.length + " articles "
                                :
                                " d'un article "
                            }
                            pour un total de : {order.totalPrice} €
                        </p>
                    </div>
                </div>
            </div>
            {modalOpen &&
                <OrderModal
                    order={order}
                    setModalOpen={setModalOpen}
                />
            }

        </>
    );
}

