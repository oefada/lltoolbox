<?php
class Notes extends AppModel {

	var $name = 'Notes';
	var $useTable = 'notes';
	var $primaryKey = 'notesId';
	var $displayField = 'notes';

    public function getNotesByPgBooking($pgBookingId)
    {
        $sql = "SELECT * FROM notes ";
        $sql .= " where pgBookingId = ".$pgBookingId . " and noteId = 1";

        $result = $this->query($sql);

        return $result;
    }
    public function updateNote($note, $pgBookingId)
    {
        $ticketNotes = Sanitize::Escape($note);
        $sql = "UPDATE notes ";

        $sql .= " set note = '" . $ticketNotes . "'";
        $sql .= " where noteId = 1 and pgBookingId = ". $pgBookingId;

        $result = $this->query($sql);

        return $result;
    }

    public function addNote($note, $pgBookingId)
    {
        $sql = "INSERT INTO notes ";
        $sql .= " (noteId, note, userId, ticketId, pgBookingId, dateCreated) ";
        $sql .= " values (1, '". $note . "','',''," .$pgBookingId . ', now())';

        $result = $this->query($sql);

        return $result;
    }


    protected static function getEntityColumnsMap()
    {
        return false;
    }

}
?>
