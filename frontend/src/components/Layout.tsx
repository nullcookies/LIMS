import { Outlet, useNavigate, useLocation } from 'react-router-dom';
import {
  AppBar, Box, CssBaseline, Drawer, IconButton, List, ListItem,
  ListItemButton, ListItemIcon, ListItemText, Toolbar, Typography, Button,
} from '@mui/material';
import {
  Menu as MenuIcon, Dashboard, Science, Assignment, People,
  Biotech, Assessment, Logout,
} from '@mui/icons-material';
import { useState } from 'react';
import { useAuthStore } from '../store/authStore';

const drawerWidth = 240;

const navItems = [
  { label: 'Dashboard', icon: <Dashboard />, path: '/' },
  { label: 'Samples', icon: <Science />, path: '/samples' },
  { label: 'Tests', icon: <Assignment />, path: '/tests' },
  { label: 'Customers', icon: <People />, path: '/customers' },
  { label: 'Instruments', icon: <Biotech />, path: '/instruments' },
  { label: 'Reports', icon: <Assessment />, path: '/reports' },
];

export default function Layout() {
  const [mobileOpen, setMobileOpen] = useState(false);
  const navigate = useNavigate();
  const location = useLocation();
  const { user, logout } = useAuthStore();

  const drawer = (
    <Box>
      <Toolbar>
        <Typography variant="h6" noWrap>LIMS</Typography>
      </Toolbar>
      <List>
        {navItems.map((item) => (
          <ListItem key={item.path} disablePadding>
            <ListItemButton
              selected={location.pathname === item.path}
              onClick={() => { navigate(item.path); setMobileOpen(false); }}
            >
              <ListItemIcon>{item.icon}</ListItemIcon>
              <ListItemText primary={item.label} />
            </ListItemButton>
          </ListItem>
        ))}
      </List>
    </Box>
  );

  return (
    <Box sx={{ display: 'flex' }}>
      <CssBaseline />
      <AppBar position="fixed" sx={{ zIndex: (t) => t.zIndex.drawer + 1 }}>
        <Toolbar>
          <IconButton color="inherit" edge="start" onClick={() => setMobileOpen(!mobileOpen)} sx={{ mr: 2, display: { sm: 'none' } }}>
            <MenuIcon />
          </IconButton>
          <Typography variant="h6" noWrap sx={{ flexGrow: 1 }}>
            Laboratory Information Management System
          </Typography>
          <Typography variant="body2" sx={{ mr: 2 }}>{user?.name}</Typography>
          <Button color="inherit" onClick={logout} startIcon={<Logout />}>Logout</Button>
        </Toolbar>
      </AppBar>
      <Box component="nav" sx={{ width: { sm: drawerWidth }, flexShrink: { sm: 0 } }}>
        <Drawer
          variant="permanent"
          sx={{ display: { xs: 'none', sm: 'block' }, '& .MuiDrawer-paper': { boxSizing: 'border-box', width: drawerWidth } }}
          open
        >
          {drawer}
        </Drawer>
      </Box>
      <Box component="main" sx={{ flexGrow: 1, p: 3, width: { sm: `calc(100% - ${drawerWidth}px)` } }}>
        <Toolbar />
        <Outlet />
      </Box>
    </Box>
  );
}
