import React, { useState, useEffect } from "react";
import { useParams } from "react-router-dom";
import axios from 'axios';
import Styles from '../Styles/Detaille.module.css';

interface Product {
    id: number;
    name: string;
    description: string;
    photo: string;
    price: number;
}

const Detail = () => {
    const { id } = useParams<{ id: string }>();
    const [product, setProduct] = useState<Product | null>(null);

    useEffect(() => {
        axios.get(`http://127.0.0.1:8000/api/products/${id}`)
            .then(response => {
                const data = response.data as Product;
                setProduct(data);
                console.log(data);
            })
            .catch(error => {
                console.error('Une erreur s\'est produite : ', error);
            });
    }, [id]);

    if (!product) {
        return <p>Loading...</p>;
    }

    return (
        <div className={Styles.ctn} >

            <div className={Styles.ctn2}>

                <div className={Styles.boxMid}>

                    <img src={product.photo} alt={product.name} className={Styles.img} />
                </div>

                <div className={Styles.boxRight}>


                    <p style={{ fontSize: '30px', borderBottom: '1px solid black' }}> {product.name}  </p> {/* Afficher les détails du produit */}
                    <p style={{ fontSize: '20px', }}> {product.description}</p>
                    <p> {product.price} $ </p>
                </div>
            </div>

        </div>
    );
};

export default Detail;
