import React, { useState } from 'react';
import { TextField, IconButton, InputAdornment } from '@mui/material';
import { Search as SearchIcon } from '@mui/icons-material';

interface SearchBarProps {
    onSearch: (query: string) => void;
}

/**
 * Renders a search bar component.
 *
 * @param {Object} onSearch - Callback function to be called when the search button is clicked or the enter key is pressed.
 * @param {string} onSearch.query - The search query entered by the user.
 * @return {JSX.Element} The search bar component.
 */
export default function SearchBar({ onSearch }: SearchBarProps): JSX.Element {

    const [query, setQuery] = useState<string>('');

    const handleInputChange = (event: React.ChangeEvent<HTMLInputElement>) => {
        setQuery(event.target.value);
    };

    const handleSearch = () => {
        onSearch(query);
    };

    const handleKeyPress = (event: React.KeyboardEvent<HTMLInputElement>) => {
        if (event.key === 'Enter') {
            handleSearch();
        }
    };

    return (
        <div style={{ backgroundColor: 'white' }}>
            <TextField
                placeholder="Search..."
                value={query}
                onChange={handleInputChange}
                onKeyPress={handleKeyPress}
                InputProps={{
                    endAdornment: (
                        <InputAdornment position="end">
                            <IconButton onClick={handleSearch}>
                                <SearchIcon />
                            </IconButton>
                        </InputAdornment>
                    ),
                }}
            />
        </div>
    );
};

