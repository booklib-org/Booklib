<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Library;
use Illuminate\Http\Request;

class OPDSController extends Controller
{
    public function ShowMainLibraries(){

        $libraries = Library::all();

        $return = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
        <feed xmlns=\"http://www.w3.org/2005/Atom\" xml:lang=\"en\" xmlns:dc=\"http://purl.org/dc/terms/\" xmlns:opds=\"http://opds-spec.org/2010/catalog\">
            <id>0</id>
	        <title>" . getenv("APP_NAME") . " -  Library Overview</title>

            <author>
                <name>Booklib.org Library System</name>
                <uri>https://booklib.org/</uri>
            </author>
            <link type=\"application/atom+xml; profile=opds-catalog; kind=navigation\" rel=\"self\" href=\"/opds/\"/>
            <link type=\"application/atom+xml; profile=opds-catalog; kind=navigation\" rel=\"start\" href=\"/opds/\"/>
            ";

        foreach($libraries as $library){
            $return = $return . "<entry>
                <title>" . $library->name . "  Library</title>
                <id>" . $library->id . "</id>

                <content type=\"html\">All books</content>
                <link type=\"application/atom+xml; profile=opds-catalog; kind=navigation\" rel=\"subsection\" href=\"/opds-$library->id\"/>
            </entry>";
        }

        $return = $return . "</feed>";

        return $return;

    }


    public function ShowMain($id){

        $library = Library::findOrFail($id);

        return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
        <feed xmlns=\"http://www.w3.org/2005/Atom\" xml:lang=\"en\" xmlns:dc=\"http://purl.org/dc/terms/\" xmlns:opds=\"http://opds-spec.org/2010/catalog\">
            <id>$id</id>
	        <title>" . getenv("APP_NAME") . " - " . $library->name . " Library</title>
            <updated>" . $library->updated_at . "</updated>
            <author>
                <name>Booklib.org Library System</name>
                <uri>https://booklib.org/</uri>
            </author>
            <link type=\"application/atom+xml; profile=opds-catalog; kind=navigation\" rel=\"self\" href=\"/opds-$id/\"/>
            <link type=\"application/atom+xml; profile=opds-catalog; kind=navigation\" rel=\"start\" href=\"/opds-$id/\"/>
            <entry>
                <title>All books</title>
                <id>$id-allBooks</id>
                <updated>" . File::where("library_id", "=", $id)->orderBy("id", "DESC")->first()->updated_at . "</updated>
                <content type=\"html\">All books</content>
                <link type=\"application/atom+xml; profile=opds-catalog; kind=acquisition\" rel=\"subsection\" href=\"/opds-$id/all\"/>
            </entry>
            <entry>
                <title>Folders</title>
                <id>$id-allFolders</id>
                <updated>" . File::where("library_id", "=", $id)->orderBy("id", "DESC")->first()->updated_at . "</updated>
                <content type=\"html\">All books grouped by folder.</content>
                <link type=\"application/atom+xml; profile=opds-catalog; kind=navigation\" rel=\"subsection\" href=\"/opds-$id/folders\"/>
            </entry>
            <entry>
                <title>Latest</title>
                <id>$id-latest</id>
                <updated>" . File::where("library_id", "=", $id)->orderBy("id", "DESC")->first()->updated_at . "</updated>
                <content type=\"html\">Latest books added to the collection</content>
                <link type=\"application/atom+xml; profile=opds-catalog; kind=acquisition\" rel=\"subsection\" href=\"/opds-$id/latest\"/>
            </entry>
        </feed>";

    }

    public function ShowLatest($id){
        $library = Library::findOrFail($id);

        $return = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
        <feed xmlns=\"http://www.w3.org/2005/Atom\" xml:lang=\"en\"  xmlns:dc=\"http://purl.org/dc/terms/\" xmlns:opds=\"http://opds-spec.org/2010/catalog\">
            <id>$id</id>
	        <title>" . getenv("APP_NAME") . " - " . $library->name . " - 50 Latest Books</title>

            <author>
                <name>Booklib.org Library System</name>
                <uri>https://booklib.org/</uri>
            </author>
            <link type=\"application/atom+xml; profile=opds-catalog; kind=navigation\" rel=\"self\" href=\"/opds-$id/latest\"/>
            <link type=\"application/atom+xml; profile=opds-catalog; kind=navigation\" rel=\"start\" href=\"/opds-$id/latest\"/>

            ";

        foreach(File::where("library_id", "=", $id)->orderBy("id", "DESC")->take(50)->get() as $file){
            $return = $return . "
            <entry>
                    <title>" . str_replace("&", "", $file->titleOrFilename()) . "</title>
                    <id>" . $file->id . "</id>
                    <author>
                        <name>" . $file->author() . "</name>
                    </author>
                    <updated>" . $file->updated_at . "</updated>
                    <content type=\"html\">" . $file->description() . "</content>
                    <dc:language>" . $file->language() . "</dc:language>
                    <link rel=\"http://opds-spec.org/acquisition\"   href=\"/opds-download/$file->id/download?type=." . pathinfo($file->filename, PATHINFO_EXTENSION) . "\"/>
                ";

            if(isset($file->thumbnail)){
                $return = $return . "<link type=\"image/jpeg\" rel=\"http://opds-spec.org/image/thumbnail\" href=\"/storage" . str_replace("app/public", "", $file->thumbnail->storage_path) . $file->thumbnail->filename . "\"/>
                ";
            }

            $return = $return . "</entry>
                ";
        }

            $return = $return . "</feed>";

            return $return;
    }

}
