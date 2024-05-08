import React, { useEffect, useState } from 'react';
import { useAuth } from "../../Components/Authentication/AuthProvider";
import axios from "axios";
import Style from '../../Styles/User/MyProduct.module.css';
import ActionAreaCard from "../../Components/Card";
import { IconButton, Menu, MenuItem } from "@mui/material";
import MoreVertIcon from '@mui/icons-material/MoreVert';


export type Product = {
    id: number,
    description: string,
    name: string,
    photo: string
    sold: boolean,
    price: number,
}

const options = [
    'Prix croissants',
    'Prix décroissants',
];

const ITEM_HEIGHT = 48;

export default function MyProduct(): React.JSX.Element {
    const [products, setProducts] = useState<Product[] | null>(null);
    const [filteredProducts, setFilteredProducts] = useState<Product[] | null>(null);
    const [filter, setFilter] = useState<string>("all")
    const authProvider = useAuth();
    const [anchorEl, setAnchorEl] = React.useState<null | HTMLElement>(null);
    const open = Boolean(anchorEl);
    const handleClick = (event: React.MouseEvent<HTMLElement>) => {
        setAnchorEl(event.currentTarget);
    };
    const handleClickFilter = (event: React.MouseEvent<HTMLElement>, index: number) => {
        handleClose();
        switch (index) {
            case 0:
                filteredProducts?.sort((a, b) => a.price - b.price);
                break;
            case 1:
                filteredProducts?.sort((a, b) => b.price - a.price);
                break;
        }
    }
    const handleClose = () => {
        setAnchorEl(null);
    };
    useEffect(() => {
        const fetchData = async () => {
            try {
                if (authProvider.getDecodedToken() !== null) {
                    const response = await axios.get(`http://127.0.0.1:8000/api/products/user/${authProvider.getDecodedToken()?.username}`, {
                        headers: {
                            Authorization: `Bearer ${authProvider.getToken()}`,
                        },
                    });
                    const data = await response.data;
                    setProducts(data)
                    setFilteredProducts(data);
                }

            } catch (error) {
                console.error('Error fetching data:', error);
            }
        }
        fetchData();
    }, [])

    const handleFilter = (e: React.MouseEvent<HTMLAnchorElement>, filterBy: string) => {
        setFilter(filterBy);
        switch (filterBy) {
            case "all":
                setFilteredProducts(products);
                break;
            case "not-sold":
                setFilteredProducts(products && products.filter((product) => !product?.sold));
                break;
            case "sold":
                setFilteredProducts(products && products.filter((product) => product?.sold));
                break;
            default:
                setFilteredProducts(products);
                break;
        }
    }

    return (
        <div className={Style.container}>
            <div className={Style.box}>
                <div className={Style.title}>
                    <h1>Vos produits</h1>
                </div>
                <div className={Style.filterGrp}>
                    <div className={Style.filterBtns}>
                        <a href='#' onClick={(e) => handleFilter(e, "all")} className={Style.filterBtn + " " + (filter === "all" && Style.btnActive)} >Tous</a>
                        <a href='#' onClick={(e) => handleFilter(e, "not-sold")} className={Style.filterBtn + " " + (filter === "not-sold" && Style.btnActive)} >En ligne</a>
                        <a href='#' onClick={(e) => handleFilter(e, "sold")} className={Style.filterBtn + " " + (filter === "sold" && Style.btnActive)}>Vendu</a>
                    </div>
                    <div>
                        <IconButton
                            aria-label="more"
                            id="long-button"
                            aria-controls={open ? 'long-menu' : undefined}
                            aria-expanded={open ? 'true' : undefined}
                            aria-haspopup="true"
                            onClick={handleClick}
                        >
                            <MoreVertIcon className={Style.vertIcon} />
                        </IconButton>
                        <Menu
                            id="long-menu"
                            MenuListProps={{
                                'aria-labelledby': 'long-button',
                            }}
                            anchorEl={anchorEl}
                            open={open}
                            onClose={handleClose}
                            PaperProps={{
                                style: {
                                    maxHeight: ITEM_HEIGHT * 4.5,
                                    width: '20ch',
                                },
                            }}
                        >
                            {options.map((option, index) => (
                                <MenuItem key={option} onClick={(e) => handleClickFilter(e, index)}>
                                    {option}
                                </MenuItem>
                            ))}
                        </Menu>
                    </div>
                </div>
                <div className={Style.products}>
                    {filteredProducts && filteredProducts.map((product) => {
                        return (
                            <div key={product.id}>
                                <ActionAreaCard image={product.photo} title={product.name} price={product.price} isMyProduct={true} description={product.description} id={product.id} />
                            </div>
                        )
                    })
                    }
                </div>
            </div>
        </div>
    );
}