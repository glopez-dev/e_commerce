import React, { useEffect, useState } from 'react';
import Style from '../Styles/Panier.module.css';
import axios, { AxiosResponse } from 'axios';
import ArticlePanier from '../Components/ArticlePanier';
import Button from '@mui/material/Button';
import { useAuth } from '../Components/Authentication/AuthProvider';
import { redirect } from "react-router-dom";
import { Link } from 'react-router-dom';
import DeleteIcon from '@mui/icons-material/Delete';

interface Article {
    id: number;
    name: string;
    description: string;
    photo: string;
    price: number;
}

const Paniers: React.FC = () => {
    const { getToken } = useAuth();
    const [articles, setArticles] = useState<Article[]>([]);

    const handleDelete = async (id: number) => {
        const token = getToken();
        if (token) {
            const config = {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            };

            try {
                await axios.delete(`http://127.0.0.1:8000/api/carts/${id}`, config);
                setArticles(articles.filter((article) => article.id !== id));
            }
            catch (error) {
                console.error('Erreur lors de la suppression de l\'article:', error);
            }
        }
    };

    useEffect(() => {
        const fetchData = async () => {

            const token = getToken();

            if (token) {
                try {
                    const config = { headers: { 'Authorization': `Bearer ${token}` } };
                    const response: AxiosResponse<Article[]> = await axios.get('http://127.0.0.1:8000/api/carts', config);
                    setArticles(response.data);
                } catch (error) {
                    console.error('Erreur lors du chargement des données:', error);
                }
            }
        };

        fetchData();
    }, [getToken]);


    const checkout = async () => {

        const token = getToken();
        console.log("In checkout with token : " + token);
        if (token) {
            try {
                const config = { headers: { 'Authorization': `Bearer ${token}` } };
                await axios.put('http://127.0.0.1:8000/api/carts/validate', null, config);
                const response: AxiosResponse = await axios.get('http://127.0.0.1:8000/api/stripe/checkout', config);

                console.log("Response : " + response.data.url);
                window.location.href = response.data.url

            } catch (error) {
                console.error("Erreur lors du traitement de la commande :", error);
            }
        }
    }


    return (
        <div className={Style.containers}>
            <div className={Style.containers1}>
                <div className={Style.box}>
                    <div className={Style.title}>
                        <p>Vous avez {articles.length} objets dans votre panier.</p>
                    </div>
                    <div>
                        {articles.map((article) => (
                            <div key={article.id} style={{ display: 'flex', flexDirection: 'row', justifyContent: 'space-between', borderBottom: '1px solid black' }}>

                                <ArticlePanier {...article} id={article.id} />

                                <DeleteIcon onClick={() => handleDelete(article.id)} style={{ color: 'black', cursor: 'pointer', padding: '10px' }} />

                            </div>
                        ))}
                    </div>
                </div>
                <div className={Style.box2}>
                    <div className={Style.cardBuy}>
                        <div className={Style.title}>
                            <p>Vous voulez acheter les {articles.length} </p>
                        </div>
                        <Button style={{ color: 'black', borderTop: '1px solid black', width: '100%', }} variant="text">Acheter</Button>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default Paniers;
