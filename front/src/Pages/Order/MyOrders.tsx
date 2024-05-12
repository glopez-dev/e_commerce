import React, {useEffect, useState} from 'react';
import Style from '../../Styles/Order/MyOrder.module.css';
import axios from "axios";
import {useAuth} from "../../Components/Authentication/AuthProvider";
import OrderCard from "../../Components/Order/OrderCard";
import {Product} from "../User/MyProduct";

export type Order = {
    id: number,
    totalPrice: number,
    creationDate: string,
    products: Array<Product>,
}

export default function MyOrders(): React.JSX.Element {
    const [orders, setOrders] = useState<Order[] | null>(null);
    const authProvider = useAuth();

    useEffect(() => {
        const fetchOrders = async () => {
            const response = await axios.get('http://127.0.0.1:8000/api/orders', {
                headers: {
                    Authorization: `Bearer ${authProvider.getToken()}`,
                },
            });
            const data = response.data;
            if (response.status === 200) {
                return data;
            } else {
                return null;
            }
        }
        fetchOrders().then((data) => {
            setOrders(data)
        });
    }, []);

    return (
        <div className={Style.container}>
            <div className={Style.box}>
                <div className={Style.title}>
                    <h1>Vos commandes</h1>
                </div>
                <div className={Style.orders}>
                    {(orders === null || orders.length === 0) ? (
                            <p>Vous n'avez pas encore passé de commande </p>
                        ) :

                        (
                            <>
                                {orders.map((order) => (
                                    <OrderCard
                                        id={order.id}
                                        creationDate={order.creationDate}
                                        totalPrice={order.totalPrice}
                                        products={order.products}
                                    />
                                ))}
                            </>
                        )}
                </div>
            </div>
        </div>
    );
}

