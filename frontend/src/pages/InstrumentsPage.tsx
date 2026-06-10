import { Box, Typography, Table, TableBody, TableCell, TableContainer, TableHead, TableRow, Paper, Chip } from '@mui/material';
import { useQuery } from '@tanstack/react-query';
import { endpoints } from '../api/endpoints';

export default function InstrumentsPage() {
  const { data } = useQuery({
    queryKey: ['instruments'],
    queryFn: () => endpoints.instruments.list(),
  });
  const instruments = data?.data?.['hydra:member'] || [];

  return (
    <Box>
      <Typography variant="h4" gutterBottom>Instruments</Typography>
      <TableContainer component={Paper}>
        <Table>
          <TableHead>
            <TableRow>
              <TableCell>ID</TableCell>
              <TableCell>Name</TableCell>
              <TableCell>Status</TableCell>
            </TableRow>
          </TableHead>
          <TableBody>
            {instruments.map((inst: { id: number; name: string }) => (
              <TableRow key={inst.id}>
                <TableCell>{inst.id}</TableCell>
                <TableCell>{inst.name}</TableCell>
                <TableCell><Chip label="active" color="success" size="small" /></TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </TableContainer>
    </Box>
  );
}
