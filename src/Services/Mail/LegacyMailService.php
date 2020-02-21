<?php

namespace App\Services\Mail;

use \Exception;
use App\Services\Mail\DTO\GenericEMail;

/**
 * Legacy Mail Service
 * TODO à remplacer par un service de mail générique
 */
final class LegacyMailService
{

    /**
     * TODO [imap_utf8_fix description]
     * @param string $string
     * @return string
     */
    public function imap_utf8_fix($string) {
        if (strpos($string, 'unicode-1-1-utf-7') !== false) {
            return iconv('UTF-7', 'UTF-8', strtr($string, array("=?unicode-1-1-utf-7?Q?" => "", "?=" => "")));
        } else if (!empty($string)) {
            return iconv_mime_decode($string, 0, "UTF-8");
        } else {
            return $string;
        }
    }

    /**
     * Returns the body using a mailbox and the e-mail's uid
     * @param [type] $uid
     * @param [type] $imap
     * @return string
     */
    private function getBody($uid, $imap) {
        $body = $this->getPart($imap, $uid, 'TEXT/HTML');

        if (empty($body)) {
            // if HTML body is empty, try getting text body
            return $this->getPart($imap, $uid, 'TEXT/PLAIN');
        }

        return $body;
    }

    /**
     * TODO [charsetToUtf8 description]
     * @param string $imapCharset
     * @param string $str
     * @return string
     */
    private function charsetToUtf8($imapCharset, $str) {
        switch ($imapCharset) {
            case 'us-ascii':
                $charset = 'ASCII';
                break;
            case 'iso-8859-1':
                $charset = 'Windows-1252';
                break;
            case 'iso-8859-15':
            case 'windows-1252':
            case 'windows-1256':
            case 'windows-1258':
                $charset = strtoupper($imapCharset);
                break;
            case 'unicode-1-1-utf-7':
                $charset = 'UTF-7';
                break;
            case 'default':
                $charset = 'UTF-8';
                break;
            default:
                $charset = $imapCharset;
                break;
        }

        return mb_convert_encoding($str, 'UTF-8', $charset);
    }

    /**
     * TODO [getPart description]
     * @param [type] $imap
     * @param [type] $uid
     * @param string $mimetype
     * @param object $structure
     * @param int    $partNumber
     * @return string
     */
    private function getPart($imap, $uid, $mimetype, $structure = null, $partNumber = null) {
        if (is_null($structure)) {
            $structure = imap_fetchstructure($imap, $uid, FT_UID);
        }

        if ($mimetype === $this->getMimeType($structure)) {
            if (is_null($partNumber)) {
                $partNumber = 1;
            }

            $encodage = null;
            $text     = imap_fetchbody($imap, $uid, $partNumber, FT_UID);
            $texte    = null;

            foreach ($structure->parameters as $parameter) {
                if ($parameter->attribute === 'charset') {
                    $encodage = strtolower($parameter->value);
                }
            }

            switch ($structure->encoding) {
                case 3:
                    $texte = imap_base64($text);
                    break;
                case 4:
                    $texte = imap_qprint($text);
                    break;
                default:
                    $texte = $text;
                    break;
            }

            $texte = $this->charsetToUtf8($encodage, $texte);

            return $texte;
        }

        // multipart
        if ($structure->type === 1) {
            foreach ($structure->parts as $index => $subStruct) {
                $prefix = is_null($partNumber) ? '' : ($partNumber . '.');
                $data   = $this->getPart($imap, $uid, $mimetype, $subStruct, $prefix . ($index + 1));

                if (false === empty($data)) {
                    return $data;
                }
            }
        }

        return '';
    }

    /**
     * TODO [getMimeType description]
     * @param [type] $structure
     * @return string
     */
    private function getMimeType($structure) {
        $primaryMimetype = array("TEXT", "MULTIPART", "MESSAGE", "APPLICATION", "AUDIO", "IMAGE", "VIDEO", "OTHER");

        if (isset($structure->subtype)) {
            return $primaryMimetype[(int) $structure->type] . "/" . $structure->subtype;
        }

        return "TEXT/PLAIN";
    }

    /**
     * TODO [getAttachments description]
     * @param [type] $imap
     * @param [type] $mailNum
     * @param [type] $part
     * @param string $section
     * @return array
     */
    private function getAttachments($imap, $mailNum, $part, string $section) {
        $attachments = [];

        if ($part->subtype === 'RFC822') {
            return [
                'name'    => 'message.eml',
                'partNum' => $section,
                'enc'     => imap_bodystruct($imap, $mailNum, $section)->encoding
            ];
        }

        if (isset($part->parts)) {
            foreach ($part->parts as $key => $subpart) {
                $newPartNum = empty($section) ? ($key + 1) : ($section . '.' . ($key + 1));
                $results    = $this->getAttachments($imap, $mailNum, $subpart, $newPartNum);

                if (count($results) != 0) {
                    if (isset($results[0]) && is_array($results[0])) {
                        foreach ($results as $result) {
                            array_push($attachments, $result);
                        }
                    } else {
                        $attachments[] = $results;
                    }
                }
            }
        } else if ($this->isAttachment($part)) {
            switch ($part->encoding) {
                case 3: // BASE64
                    $content = base64_decode(imap_fetchbody($imap, $mailNum, $section));
                    break;
                case 4: // QUOTED-PRINTABLE
                    $content = quoted_printable_decode(imap_fetchbody($imap, $mailNum, $section));
                    break;
                default:
                    $content = imap_fetchbody($imap, $mailNum, $section);
                    break;
            }

            return [
                'name'    => $this->extractAttachmentName($part),
                'partNum' => $section,
                'enc'     => $part->encoding
            ];
        }

        return $attachments;
    }

    /**
     * Tells if the given imap part is an attachment
     * @param object $part
     * @return bool
     */
    private function isAttachment($part): bool
    {
        try {
            $this->extractAttachmentName($part);
        } catch (Exception $exception) {
            return false;
        }

        return isset($part->disposition) && in_array(strtoupper($part->disposition), [ 'ATTACHMENT', 'INLINE' ]);
    }

    /**
     * Returns the attachment's name
     * @param object $part
     * @return string
     * @throws Exception if no name was found
     */
    private function extractAttachmentName($part): string
    {
        $namesFound = array_reduce(
            $this->extractParameters($part),
            function($result, $parameter) {
                $name = $this->extractParameterName($parameter);

                if (false === empty($name)) {
                    return array_merge($result, [ $name ]);
                }

                return $result;
            },
            []
        );

        if (empty($namesFound)) {
            throw new Exception('cannot extract the attachment\'s name : there is no "name" in the part data');
        }

        return trim($this->decodeToUTF8(array_slice($namesFound, 0, 1)[0]));
    }

    /**
     * Extract the "part" parameters (as "parameters" and "dparameters")
     * @param object $part
     * @return array
     */
    private function extractParameters($part): array
    {
        return array_merge(
            $part->ifdparameters === 1 ? $part->dparameters : [],
            $part->ifparameters === 1 ? $part->parameters : []
        );
    }

    /**
     * Extract the value for a "name" or "filename" parameter type
     * @param object $parameter
     * @return string the name found or an empty string
     */
    private function extractParameterName($parameter): string
    {
        if (in_array($parameter->attribute, [ 'name', 'filename' ])) {
            return $parameter->value;
        }

        return '';
    }

    /**
     *
     * =?x-unknown?B?
     * =?iso-8859-1?Q?
     * =?windows-1252?B?
     *
     * @param string $stringQP
     * @param string $base (optional) charset (IANA, lowercase)
     * @return string UTF-8
     */
    private function decodeToUTF8($stringQP, $base = 'windows-1252')
    {
        $pairs = array(
            '?x-unknown?' => "?$base?"
        );
        $stringQP = strtr($stringQP, $pairs);
        return imap_utf8($stringQP);
    }

    /**
     * TODO [getAttachment description]
     * @param [type] $mailbox
     * @param [type] $uid
     * @param [type] $partNum
     * @param string $encoding
     * @return object
     */
    private function getAttachment($mailbox, $uid, $partNum, $encoding) {
        $partStruct = imap_bodystruct($mailbox->imap(), imap_msgno($mailbox->imap(), $uid), $partNum);

        if (isset($partStruct->dparameters)) {
            $filename = $partStruct->dparameters[0]->value;
        } else {
            $filename = 'message_' . $uid . '.eml';
        }

        $message = imap_fetchbody($mailbox->imap(), $uid, $partNum, FT_UID);

        switch ($encoding) {
            case 0:
            case 1:
                $message = imap_8bit($message);
                break;
            case 2:
                $message = imap_binary($message);
                break;
            case 3:
                $message = imap_base64($message);
                break;
            case 4:
                $message = quoted_printable_decode($message);
                break;
        }

        return (object) [
            'filename' => $filename,
            'content'  => $message
        ];
    }

    /**
     * TODO [saveAttachment description]
     * @param Mailbox $mailbox
     * @param [type] $uid
     * @param [type] $partNum
     * @param string $encoding
     * @param string $path
     * @return string
     */
    public function saveAttachment(Mailbox $mailbox, $uid, $partNum, $encoding, $path)
    {
        file_put_contents(
            $path,
            $this->getAttachment($mailbox, $uid, $partNum, $encoding)->content
        );
    }

    /**
     * Returns an e-mail using a mailbox and the e-mail id
     * @param [type] $imap
     * @param [type] $id
     * @return EMail
     */
    public function getEmail($imap, $id)
    {
        $header = imap_header($imap, $id);
        $uid    = imap_uid($imap, $id);

        return new GenericEMail(
            $uid,
            $header->from[0]->mailbox . '@' . $header->from[0]->host, // from
            $this->subjectFromParts(imap_mime_header_decode($header->subject)), // subject
            $this->getBody($uid, $imap), // body
            $this->getAttachments($imap, $id, imap_fetchstructure($imap, $id), '') // attachments
        );
    }

    /**
     * Extracts the mail's subject from the imap header parts
     * @param array $parts
     * @return string
     */
    private function subjectFromParts(array $parts): string
    {
        $decodedParts = array_map(function($part) {
            return $this->charsetToUtf8($part->charset, $part->text);
        }, $parts);

        return implode('', $decodedParts);
    }

}
