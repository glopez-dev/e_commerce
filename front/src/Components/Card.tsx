import React from 'react';
import Card from '@mui/material/Card';
import CardContent from '@mui/material/CardContent';
import CardMedia from '@mui/material/CardMedia';
import Typography from '@mui/material/Typography';
import { CardActionArea } from '@mui/material';
import Button from '@mui/material/Button';

interface ActionAreaCardProps {
    image: string;
    title: string;
    description: number;
}

const ActionAreaCard: React.FC<ActionAreaCardProps> = ({ image, title, description }) => {
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
                        {description}
                    </Typography>


                </CardContent>

            </CardActionArea>
            <Button sx={{ width: '100%', }} style={{ backgroundColor: '#f6f1eb', color: 'black' }} variant="contained">Ajouter au panier</Button>

        </Card>
    );
}

export default ActionAreaCard;
