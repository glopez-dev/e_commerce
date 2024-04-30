import React, { useEffect, useState } from 'react';
import Style from '../Styles/Panier.module.css';
import axios, { AxiosResponse } from 'axios';
import ArticlePanier from '../Components/ArticlePanier';
import Button from '@mui/material/Button';
import { useAuth } from '../Components/Authentication/AuthProvider';

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

    useEffect(() => {
        const fetchData = async () => {
            const token = getToken();

            if (token) {
                const config = {
                    headers: {
                        'Authorization': `Bearer ${token}`
                    }
                };

                try {
                    const response: AxiosResponse<Article[]> = await axios.get('http://127.0.0.1:8000/api/carts', config);
                    setArticles(response.data);
                } catch (error) {
                    console.error('Erreur lors du chargement des données:', error);

                }
            }
        };

        fetchData();
    }, [getToken]);

    return (
        <div className={Style.containers}>
            <div className={Style.box}>
                <div className={Style.title}>
                    <p>Vous avez {articles.length} objets dans votre panier.</p>
                </div>

                <div>
                    {articles.map((article) => (
                        <ArticlePanier key={article.id} {...article} />
                    ))}
                </div>
            </div>

            <div className={Style.box2}>
                <div className={Style.cardBuy}>
                    <div className={Style.title}>
                        <p>Vous voulez acheter les {articles.length} </p>
                    </div>

                    <Button style={{ color: 'black', backgroundColor: '#f6f1eb', width: '100%' }} variant="text">Acheter</Button>
                </div>
            </div>
        </div>
    );
};

export default Paniers;
