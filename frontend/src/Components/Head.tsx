import React, { useState, useEffect } from 'react';
import AppBar from '@mui/material/AppBar';
import Box from '@mui/material/Box';
import Toolbar from '@mui/material/Toolbar';
import IconButton from '@mui/material/IconButton';
import Typography from '@mui/material/Typography';
import Menu from '@mui/material/Menu';
import MenuIcon from '@mui/icons-material/Menu';
import Container from '@mui/material/Container';
import Button from '@mui/material/Button';
import Tooltip from '@mui/material/Tooltip';
import MenuItem from '@mui/material/MenuItem';
import AdbIcon from '@mui/icons-material/Adb';
import PersonIcon from '@mui/icons-material/Person';
import {Link} from 'react-router-dom';
import { useAuth } from './Authentication/AuthProvider';
import HemHess from '../assets/logo-sans-titre.png';


const settings = ['Profile', 'Account', 'Dashboard', 'Logout'];

function ResponsiveAppBar() {
    const [anchorElNav, setAnchorElNav] = useState<null | HTMLElement>(null);
    const [anchorElUser, setAnchorElUser] = useState<null | HTMLElement>(null);
    const { getToken } = useAuth();
    const { onLogout } = useAuth();

    const [userToken, setUserToken] = useState<string | null>(null);

    useEffect(() => {
        const fetchToken = async () => {
            const token = getToken();
            setUserToken(token);
        };



        fetchToken();


    }, [getToken]);

    const handleLogout = () => {
        onLogout();
        setUserToken(null);
        window.location.reload();
    }

    const handleOpenNavMenu = (event: React.MouseEvent<HTMLElement>) => {
        setAnchorElNav(event.currentTarget);
    };

    const handleOpenUserMenu = (event: React.MouseEvent<HTMLElement>) => {
        setAnchorElUser(event.currentTarget);
    };

    const handleCloseNavMenu = () => {
        setAnchorElNav(null);
    };

    const handleCloseUserMenu = () => {
        setAnchorElUser(null);
    };

    return (
        <AppBar position="fixed" sx={{ bgcolor: '#fffcf7' }}>
            <Container maxWidth="xl">
                <Toolbar disableGutters>
                    <AdbIcon sx={{ display: { xs: 'none', md: 'flex' }, mr: 1 }} />
                    <Typography
                        variant="h6"
                        noWrap
                        component="a"
                        href="#app-bar-with-responsive-menu"
                        sx={{
                            mr: 2,
                            display: { xs: 'none', md: 'flex' },
                            fontFamily: 'monospace',
                            fontWeight: 700,
                            letterSpacing: '.3rem',
                            color: 'black',
                            textDecoration: 'none',
                        }}
                    >
                        <Link to="/" style={{ color: 'black', textDecoration: 'none' }}>
                            <img src={HemHess} alt="logo" style={{ width: 50, height: 50, padding: 10 }} />
                        </Link>

                    </Typography>



                    <Box sx={{ flexGrow: 1, display: { xs: 'flex', md: 'none', justifyContent: 'space-between' } }}>

                        <IconButton
                            size="large"
                            aria-label="account of current user"
                            aria-controls="menu-appbar"
                            aria-haspopup="true"
                            onClick={handleOpenNavMenu}
                            style={{ color: 'black', borderColor: 'red' }}
                        >
                            <MenuIcon />
                        </IconButton>

                        <Menu
                            id="menu-appbar"
                            anchorEl={anchorElNav}
                            anchorOrigin={{
                                vertical: 'bottom',
                                horizontal: 'left',
                            }}
                            keepMounted
                            transformOrigin={{
                                vertical: 'top',
                                horizontal: 'left',
                            }}
                            open={Boolean(anchorElNav)}
                            onClose={handleCloseNavMenu}
                            sx={{
                                display: { xs: 'block', md: 'none' },
                            }}
                        >
                            <MenuItem onClick={handleCloseNavMenu}>



                            </MenuItem>

                        </Menu>

                        <Link to="/" style={{ color: 'black', textDecoration: 'none' }}>
                            <img src={HemHess} alt="logo" style={{ width: 50, height: 50, padding: 10, }} />
                        </Link>
                    </Box>



                    <Box sx={{ flexGrow: 1, display: { xs: 'none', md: 'flex' } }}>
                        <Link to="/panier" style={{ textDecoration: 'none', color: 'black' }}>
                            <Button onClick={handleCloseNavMenu} sx={{ my: 2, color: 'black', display: 'flex' }}>
                                Panier
                            </Button>
                        </Link>

                    </Box>

                    {userToken ? (
                        <Box sx={{ flexGrow: 0 }}>
                            <Tooltip title="Open settings">
                                <IconButton onClick={handleOpenUserMenu} sx={{ p: 0 }}>
                                    <PersonIcon />
                                </IconButton>
                            </Tooltip>
                            <Menu
                                sx={{ mt: '45px' }}
                                id="menu-appbar"
                                anchorEl={anchorElUser}
                                anchorOrigin={{
                                    vertical: 'top',
                                    horizontal: 'right',
                                }}
                                keepMounted
                                transformOrigin={{
                                    vertical: 'top',
                                    horizontal: 'right',
                                }}
                                open={Boolean(anchorElUser)}
                                onClose={handleCloseUserMenu}
                            >

                                <MenuItem onClick={handleCloseUserMenu}>
                                    <Link to="/user/profil" style={{ textDecoration: 'none', color: 'black', width: '100%' }}>
                                        <Typography>Profil</Typography>
                                    </Link>
                                </MenuItem>
                                <MenuItem onClick={handleCloseUserMenu}>
                                    <Link to="/user/products" style={{ textDecoration: 'none', color: 'black',  width: '100%'}}>
                                        <Typography>Mes produits</Typography>
                                    </Link>
                                </MenuItem>
                                <MenuItem onClick={handleCloseUserMenu}>
                                    <Link to="/user/orders" style={{ textDecoration: 'none', color: 'black', width: '100%' }}>
                                        <Typography textAlign="center">Mes Commandes</Typography>
                                    </Link>
                                </MenuItem>
                                <MenuItem onClick={handleCloseNavMenu}>
                                    <Link to="/addArticle" style={{ textDecoration: 'none', color: 'black', width: '100%' }}>
                                        <Typography textAlign="center">Ajouter un article</Typography>
                                    </Link>
                                </MenuItem>
                                <MenuItem onClick={handleCloseUserMenu}>
                                    <Typography onClick={handleLogout} textAlign="center">Déconnexion</Typography>
                                </MenuItem>
                            </Menu>
                        </Box>
                    ) : (
                        <Box sx={{ flexGrow: 0 }}>
                            <Link to="/login" style={{ textDecoration: 'none', color: 'black' }}>
                                <Button sx={{ my: 2, color: 'black', display: 'flex' }}>Se connecter</Button>

                            </Link>
                        </Box>
                    )}
                </Toolbar>
            </Container>
        </AppBar >

    );
}

export default ResponsiveAppBar;
