import React from 'react';
import Style from '../Styles/Panier.module.css';
import { Link } from 'react-router-dom';

interface ArticleProps {
    name: string;
    description: string;
    photo: string;
    price: number;
    id: number;


}

const ArticleComponent: React.FC<ArticleProps> = ({ name, description, photo, price, id }) => {
    return (


        <div className={Style.article}>
            <Link to={`/detail/${id}`} style={{ textDecoration: 'none', color: 'black' }}>
                <div className={Style.box1}>
                    <div className={Style.img}>
                        <img className={Style.img1} src={photo} alt={name} />
                    </div>

                    <div className={Style.detail}>
                        <p>{name}</p>
                        <p>{description}</p>
                        <p>{price} $</p>

                    </div>
                </div>
            </Link>
        </div>
    );
};

export default ArticleComponent;
