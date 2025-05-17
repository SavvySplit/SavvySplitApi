
// resources/js/Pages/Emails/Show.jsx
import AppLayout from '@/layouts/AppLayout'
import { Link } from '@inertiajs/react'
import { Document, Page, pdfjs } from 'react-pdf';
import { useState } from 'react';
import CloseIcon from '@mui/icons-material/Close';
import {
  Stepper,
  Step,
  StepLabel,
  Box,
  Typography,
  Paper,
  Button,
  Dialog, DialogContent, IconButton,
} from '@mui/material';

import workerSrc from 'pdfjs-dist/build/pdf.worker.min.mjs?url';
import 'react-pdf/dist/Page/AnnotationLayer.css';
import 'react-pdf/dist/Page/TextLayer.css';
import OCRViewer from './OCRViewer';
import DynamicJsonRenderer from './DynamicJsonRenderer';

pdfjs.GlobalWorkerOptions.workerSrc = workerSrc





export default function Show({ email }) {
  const steps = ['Received', 'OCRed', 'Processed with AI', 'Classified', 'Item Generated']
  const [openViewer, setOpenViewer] = useState(false);
  const [selectedFile, setSelectedFile] = useState(null);
  const [numPages, setNumPages] = useState(null);
  const [pageNumber, setPageNumber] = useState(1);

  // Determine current step index (example logic — you should adapt to actual status logic)
  const currentStatus = email.status || 'Received'
  const stepIndex = steps.findIndex(s => s.toLowerCase() === currentStatus.toLowerCase())
  const [openOCRViewer, setOpenOCRViewer] = useState(false);
  const [selectedOCRFile, setSelectedOCRFile] = useState<{ url: string, isPDF: boolean } | null>(null);


  return (
    <AppLayout>
      <Box sx={styles.container}>
        <Typography variant="h4" sx={styles.subject}>{email.subject}</Typography>

        <Paper elevation={2} sx={{ padding: 3, marginBottom: 4, backgroundColor: '#f9fafb' }}>
          <Typography variant="subtitle1" sx={{ marginBottom: 2, fontWeight: '600', color: '#374151' }}>Email Status</Typography>

          <Stepper activeStep={stepIndex >= 0 ? stepIndex : 0} alternativeLabel sx={{ paddingX: 1 }}>
            {steps.map((label, idx) => (
              <Step key={label}>
                <StepLabel
                  StepIconProps={{
                    sx: {
                      color: idx <= stepIndex ? '#3b82f6' : '#d1d5db',
                      '&.Mui-completed': { color: '#10b981' },
                      '&.Mui-active': {
                        color: '#3b82f6',
                        transform: 'scale(1.2)',
                      },
                    }
                  }}
                >
                  <Typography
                    variant="caption"
                    sx={{
                      fontWeight: idx === stepIndex ? 600 : 400,
                      color: idx === stepIndex ? '#1f2937' : '#6b7280',
                      fontSize: '0.8rem',
                    }}
                  >
                    {label}
                  </Typography>
                </StepLabel>
              </Step>
            ))}
          </Stepper>
        </Paper>


        <Box sx={styles.meta}>
          <Typography><strong>From:</strong> {email.from}</Typography>
          {/* <Typography><strong>To:</strong> {email.to}</Typography> */}
          <Typography><strong>Date:</strong> {new Date(email.created_at).toLocaleString()}</Typography>
        </Box>

        <Box sx={styles.bodyContainer}>
          <Typography variant="subtitle1" sx={styles.sectionTitle}>Message</Typography>
          <Paper variant="outlined" sx={styles.body}>
            <pre>{email.body}</pre>
          </Paper>
        </Box>

        {email.attachments?.length > 0 && (
          <Box sx={styles.attachmentsContainer}>
            <Typography variant="subtitle1" sx={styles.sectionTitle}>Attachments</Typography>
            <ul style={styles.attachmentList}>
              {email.attachments.map((file, index) => {
                const isImage = /\.(jpg|jpeg|png|gif)$/i.test(file.filename);
                const isPDF = /\.pdf$/i.test(file.filename);

                return (
                  <li key={index} style={styles.attachmentItem}>
                    <Box sx={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', flexWrap: 'wrap' }}>
                      <span style={styles.attachmentItemFileName}>📎 {file.filename}</span>
                      <Box>
                        <a href={file.url} style={styles.attachmentLink} download>Download</a>
                        {(isImage || isPDF) && (
                          <a
                            onClick={() => {
                              setSelectedFile({ ...file, isPDF });
                              setPageNumber(1);
                              setOpenViewer(true);
                            }}
                            style={{ ...styles.attachmentLink, marginLeft: 12, cursor: 'pointer' }}
                          >
                            View
                          </a>
                        )}

                        <a onClick={() => {
                          setSelectedOCRFile({ url: file.url, isPDF });
                          setOpenOCRViewer(true);
                        }}
                          style={{ ...styles.attachmentLink, marginLeft: 12, cursor: 'pointer' }}
                        >
                          OCR/AI Compare
                        </a>
                      </Box>
                    </Box>
                  </li>
                );
              })}
            </ul>
          </Box>
        )}


        {/* <OCRViewer fileUrl={email.attachments[0].url} ocrText={email.ocr_text} /> */}

        <Box sx={{ marginTop: 4 }}>
          {/* OCR Output */}
          <Paper elevation={1} sx={{ padding: 3, marginBottom: 3, backgroundColor: '#f9fafb' }}>
            <Typography variant="subtitle1" sx={{ fontWeight: 600, marginBottom: 1 }}>
              OCR Output
            </Typography>
            <Typography variant="body2" sx={{ whiteSpace: 'pre-wrap', color: '#374151' }}>
              {email.ocr_text || 'No OCR result available.'}
            </Typography>
          </Paper>

          {/* AI Processed Result */}
          <Paper elevation={1} sx={{ padding: 3, backgroundColor: '#f3f4f6' }}>
            <Typography variant="subtitle1" sx={{ fontWeight: 600, marginBottom: 1 }}>
              AI Analysis
            </Typography>
            <Typography variant="body2" sx={{ whiteSpace: 'pre-wrap', color: '#374151' }}>
              {email.ai_result || 'No AI analysis available.'}
            </Typography>
          </Paper>
        </Box>
          {email.ai_result && email.ai_result.trim().startsWith('{')  && (
            <Paper sx={{ p: 2, backgroundColor: '#f4f6f8', mt: 2 }}>
              <Typography variant="h6" gutterBottom>
                AI Extracted Data
              </Typography>
               <DynamicJsonRenderer data={JSON.parse(email.ai_result)} />
             </Paper>
          )}



        <Box sx={{ marginTop: 4 }}>
          <Link href="/" style={styles.backLink}>← Back to Dashboard</Link>
        </Box>
      </Box>

      <Dialog open={openViewer} onClose={() => setOpenViewer(false)} maxWidth="md" fullWidth>
        <DialogContent sx={{ position: 'relative', padding: 0 }}>
          <IconButton onClick={() => setOpenViewer(false)} sx={{ position: 'absolute', top: 8, right: 8 }}>
            <CloseIcon />
          </IconButton>

          {selectedFile?.isPDF ? (
            <Box sx={{ padding: 4 }}>
              <Document
                file={selectedFile.url}
                onLoadSuccess={({ numPages }) => setNumPages(numPages)}
                loading="Loading PDF..."
              >
                <Page pageNumber={pageNumber} />
              </Document>

              <Box sx={{ display: 'flex', justifyContent: 'space-between', marginTop: 2 }}>
                <button onClick={() => setPageNumber(p => Math.max(p - 1, 1))} disabled={pageNumber === 1}>Previous</button>
                <Typography>Page {pageNumber} of {numPages}</Typography>
                <button onClick={() => setPageNumber(p => Math.min(p + 1, numPages))} disabled={pageNumber === numPages}>Next</button>
              </Box>
            </Box>
          ) : (
            <img src={selectedFile?.url} alt={selectedFile?.filename} style={{ width: '100%', maxHeight: '90vh', objectFit: 'contain' }} />
          )}
        </DialogContent>
      </Dialog>

      <Dialog open={openOCRViewer} onClose={() => setOpenOCRViewer(false)} maxWidth="xl" fullWidth>
        <Box sx={{ padding: 2, position: 'relative' }}>
          <IconButton
            onClick={() => setOpenOCRViewer(false)}
            sx={{ position: 'absolute', top: 8, right: 8 }}
          >
            <CloseIcon />
          </IconButton>

          {selectedOCRFile && (
            <OCRViewer
              fileUrl={selectedOCRFile.url}
              ocrText={email.ocr_text}
              aiResult={email.ai_result}
            />
            
          )}
        </Box>
      </Dialog>



    </AppLayout>



  )



}



const styles = {
  container: {
    maxWidth: '800px',
    margin: '0 auto',
    padding: '40px 20px',
    backgroundColor: '#fff',
    borderRadius: '12px',
    boxShadow: '0 1px 4px rgba(0,0,0,0.08)',
  },
  subject: {
    marginBottom: '20px',
  },
  meta: {
    fontSize: '14px',
    marginBottom: '24px',
    lineHeight: '1.6',
    color: '#4B5563',
  },
  bodyContainer: {
    marginBottom: '30px',
  },
  sectionTitle: {
    fontWeight: '600',
    marginBottom: '10px',
    color: '#374151',
  },
  body: {
    padding: '16px',
    borderRadius: '8px',
    backgroundColor: '#f9fafb',
    fontSize: '15px',
    whiteSpace: 'pre-wrap',
    fontFamily: 'inherit',
  },
  attachmentsContainer: {
    marginBottom: '30px',
  },
  attachmentList: {
    listStyle: 'none',
    paddingLeft: 0,
  },
  attachmentItem: {
    display: 'flex',
    justifyContent: 'space-between',
    alignItems: 'center',
    padding: '8px 0',
    borderBottom: '1px solid #e5e7eb',
    fontSize: '14px',
  },
  attachmentItemFileName: {
    maxWidth: '500px',
    overflow: 'hidden',
    textOverflow: 'ellipsis',
    whiteSpace: 'nowrap',
    marginRight: 2,
  },
  attachmentLink: {
    color: '#2563eb',
    textDecoration: 'none',
    marginLeft: '12px',
  },
  backLink: {
    fontSize: '14px',
    color: '#2563eb',
    textDecoration: 'none',
  },
}
