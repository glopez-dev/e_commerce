import React from 'react';
import Card from '@mui/material/Card';
import CardContent from '@mui/material/CardContent';
import CardMedia from '@mui/material/CardMedia';
import Typography from '@mui/material/Typography';
import { CardActionArea } from '@mui/material';

interface ActionAreaCardProps {
    image: string;
    title: string;
    description: number;
}

/**
 * Renders an ActionAreaCard component with the provided image, title, and description.
 *
 * @param {string} image - The URL of the image to display.
 * @param {string} title - The title of the card.
 * @param {number} description - The description of the card.
 * @return {JSX.Element} The rendered ActionAreaCard component.
 */
export default function ActionAreaCard({ image, title, description }: ActionAreaCardProps): JSX.Element {
    return (
        <Card sx={{ width: 345 }}>
            <CardActionArea>
                <CardMedia
                    component="img"
                    height="340"
                    image={image}
                    alt={title}
                />
                <CardContent>
                    <Typography gutterBottom variant="h5" component="div">
                        {title}
                    </Typography>
                    <Typography variant="body2" color="text.secondary">
                        {description}
                    </Typography>
                </CardContent>
            </CardActionArea>
        </Card>
    );
}
