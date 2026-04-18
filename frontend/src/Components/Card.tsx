import React from 'react';
import Card from '@mui/material/Card';
import CardContent from '@mui/material/CardContent';
import CardMedia from '@mui/material/CardMedia';
import Typography from '@mui/material/Typography';
import { CardActionArea } from '@mui/material';
import Button from '@mui/material/Button';
import axios from 'axios';
import { useAuth } from './Authentication/AuthProvider';
import {API_BASE_URL} from "../config";
import { Link } from 'react-router-dom';


interface ActionAreaCardProps {
    image: string;
    title: string;
    isMyProduct: boolean;
    description: string;
    price: number;
    id: number;

}



const ActionAreaCard: React.FC<ActionAreaCardProps> = ({ image, title, price, description, id, isMyProduct = false }) => {

    const { getToken } = useAuth();

    const handleAddToCart = () => {
        const token = getToken();

        if (token) {
            const config = {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            };

            axios.post(`${API_BASE_URL}/api/carts/${id}`, null, config)
                .catch(() => {});
        }
    };
    return (
        <Card sx={{ width: 245 }}>
            <Link to={`/detail/${id}`} style={{ textDecoration: 'none', color: 'black' }}>
                <CardActionArea>
                    <CardMedia
                        component="img"
                        height="200"
                        image={image}
                        alt={title}
                    />
                    <CardContent style={{ backgroundColor: '#f6f1eb' }}>
                        <Typography gutterBottom variant="h5" component="div">
                            {title}
                        </Typography>
                        <Typography variant="body2" color="text.secondary">
                            {price}
                        </Typography>
                        <Typography variant="body2" color="text.secondary">
                            {description}
                        </Typography>



                    </CardContent>

                </CardActionArea>
            </Link>
            {!isMyProduct && <Button onClick={handleAddToCart} sx={{ width: '100%', }} style={{ backgroundColor: '#f6f1eb', color: 'black' }} variant="contained">Ajouter au panier</Button>}

        </Card>
    );
}

export default ActionAreaCard;
