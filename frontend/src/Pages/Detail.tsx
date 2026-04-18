import React, { useState, useEffect } from "react";
import {useNavigate, useParams} from "react-router-dom";
import axios from 'axios';
import Styles from '../Styles/Detaille.module.css';
import {useAuth} from "../Components/Authentication/AuthProvider";
import Button from "@mui/material/Button";

interface Product {
    id: number;
    name: string;
    description: string;
    photo: string;
    price: number;
    seller: string;
}

const Detail = () => {
    const { id } = useParams<{ id: string }>();
    const [product, setProduct] = useState<Product | null>(null);
    const authProvider = useAuth();
    const decodedToken = authProvider.getDecodedToken();
    const navigate = useNavigate();
    useEffect(() => {
        axios.get(`http://127.0.0.1:8000/api/products/${id}`)
            .then(response => {
                const data = response.data as Product;
                setProduct(data);
            })
            .catch(error => {
                console.error('Une erreur s\'est produite : ', error);
            });
    }, [id]);

    if (!product) {
        return <p>Loading...</p>;
    }

    const handleDelete = async () => {
        const response = await axios.delete(`http://127.0.0.1:8000/api/products/${id}`, {
            headers: {
                Authorization: `Bearer ${authProvider.getToken()}`,
            },
        });
        if (response.status === 202) {
           navigate('/')
        } else {
            console.error('Une erreur s\'est produite : ', response);
        }
    }

    return (
        <div className={Styles.ctn} >

            <div className={Styles.ctn2}>

                <div className={Styles.boxMid}>

                    <img src={product.photo} alt={product.name} className={Styles.img} />
                </div>

                <div className={Styles.boxRight}>

                    <p style={{ fontSize: '30px', borderBottom: '1px solid black' }}> {product.name}  </p> {/* Afficher les détails du produit */}
                    <p style={{ fontSize: '20px', }}>Vendu par : {product.seller} </p>
                    <p style={{ fontSize: '20px', }}> {product.description}</p>
                    <p> {product.price} &euro;</p>
                    {decodedToken?.username === product.seller &&
                        <Button
                            type={"button"}
                            variant="contained"
                            color="error"
                            onClick={handleDelete}
                        >
                            Supprimer
                        </Button>
                    }
                </div>
            </div>

        </div>
    );
};

export default Detail;
