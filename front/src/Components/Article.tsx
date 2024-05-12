import React, {useState, useEffect} from 'react';
import axios from 'axios';

import Style from '../Styles/Article.module.css';
import Card from './Card';
import {useAuth} from "./Authentication/AuthProvider";

interface Article {
    name: string;
    photo: string;
    description: string;
    price: number;
    id: number;

}

const Article = () => {
    const [articles, setArticles] = useState<Article[]>([]);
    const authProvider = useAuth();
    useEffect(() => {
        const fetchData = async () => {
            try {
                let config = {}
                if (authProvider.getToken() !== null) {
                    config = {
                        headers: {
                            Authorization: `Bearer ${authProvider.getToken()}`,
                        },
                    }
                }
                const response = await axios.get('http://127.0.0.1:8000/api/products', config);
                setArticles(response.data);
                console.log(response.data);
            } catch (error) {
                console.error('Error fetching data:', error);
            }
        };

        fetchData();
    }, []);

    return (
        <div className={Style.containers}>
            <div className={Style.containers1}>
                <div className={Style.title}>
                    <h1>PRODUITS</h1>

                </div>
            </div>
            <div className={Style.article}>
                <div className={Style.card}>

                    {articles && articles.map((articles, index) => (
                        <Card key={index} image={articles.photo} title={articles.name}
                              description={articles.description} price={articles.price} id={articles.id}
                              isMyProduct={false}/>
                    ))}
                </div>
            </div>
        </div>
    );
};

export default Article;
