import React from 'react';
import Card from '@mui/material/Card';
import CardContent from '@mui/material/CardContent';
import CardMedia from '@mui/material/CardMedia';
import Typography from '@mui/material/Typography';
import { CardActionArea } from '@mui/material';
import Button from '@mui/material/Button';
import axios from 'axios';
import { useAuth } from './Authentication/AuthProvider';


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

            axios.post(`http://127.0.0.1:8000/api/carts/${id}`, null, config)
                .then(response => {
                    console.log('Article ajouté au panier avec succès !');
                })
                .catch(error => {
                    console.error('Erreur lors de l\'ajout au panier :', error);
                });
        } else {
            console.error('Token d\'authentification introuvable.');
        }
    };
    return (
        <Card sx={{ width: 245 }}>
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
            {!isMyProduct && <Button onClick={handleAddToCart} sx={{ width: '100%', }} style={{ backgroundColor: '#f6f1eb', color: 'black' }} variant="contained">Ajouter au panier</Button>}

        </Card>
    );
}

export default ActionAreaCard;
