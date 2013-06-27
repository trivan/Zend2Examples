/*
 *  This file is part of Poedit (http://www.poedit.net)
 *
 *  Copyright (C) 1999-2013 Vaclav Slavik
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a
 *  copy of this software and associated documentation files (the "Software"),
 *  to deal in the Software without restriction, including without limitation
 *  the rights to use, copy, modify, merge, publish, distribute, sublicense,
 *  and/or sell copies of the Software, and to permit persons to whom the
 *  Software is furnished to do so, subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in
 *  all copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 *  FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 *  DEALINGS IN THE SOFTWARE.
 *
 */


#include <wx/wfstream.h>
#include <wx/config.h>
#include <wx/tokenzr.h>

#include "parser.h"


#include <wx/arrimpl.cpp>
WX_DEFINE_OBJARRAY(ParserArray);

void ParsersDB::Read(wxConfigBase *cfg)
{
    Clear();
    
    cfg->SetExpandEnvVars(false);

    Parser info;
    wxString key, oldpath = cfg->GetPath();
    wxStringTokenizer tkn(cfg->Read(_T("Parsers/List"), wxEmptyString), _T(";"));

    while (tkn.HasMoreTokens())
    {
        info.Name = tkn.GetNextToken();
        key = info.Name; key.Replace(_T("/"), _T("_"));
        cfg->SetPath(_T("Parsers/") + key);
        info.Extensions = cfg->Read(_T("Extensions"), wxEmptyString);
        info.Command = cfg->Read(_T("Command"), wxEmptyString);
        info.KeywordItem = cfg->Read(_T("KeywordItem"), wxEmptyString);
        info.FileItem = cfg->Read(_T("FileItem"), wxEmptyString);
        info.CharsetItem = cfg->Read(_T("CharsetItem"), wxEmptyString);
        Add(info);
        cfg->SetPath(oldpath);
    }
}



void ParsersDB::Write(wxConfigBase *cfg)
{
#if 0 // asserts on wxGTK, some bug in wx
    if (cfg->HasGroup(_T("Parsers")))
        cfg->DeleteGroup(_T("Parsers"));
#endif

    cfg->SetExpandEnvVars(false);
    
    if (GetCount() == 0) return;
    
    size_t i;
    wxString list;
    list << Item(0).Name;
    for (i = 1; i < GetCount(); i++)
        list << _T(";") << Item(i).Name;
    cfg->Write(_T("Parsers/List"), list);
    
    wxString oldpath = cfg->GetPath();
    wxString key;
    for (i = 0; i < GetCount(); i++)
    {
        key = Item(i).Name; key.Replace(_T("/"), _T("_"));
        cfg->SetPath(_T("Parsers/") + key);
        cfg->Write(_T("Extensions"), Item(i).Extensions);
        cfg->Write(_T("Command"), Item(i).Command);
        cfg->Write(_T("KeywordItem"), Item(i).KeywordItem);
        cfg->Write(_T("FileItem"), Item(i).FileItem);
        cfg->Write(_T("CharsetItem"), Item(i).CharsetItem);
        cfg->SetPath(oldpath);
    }
}
    
int ParsersDB::FindParser(const wxString& name)
{
    for (size_t i = 0; i < GetCount(); i++)
    {
        if ((*this)[i].Name == name)
            return int(i);
    }
    return -1;
}


wxArrayString Parser::SelectParsable(const wxArrayString& files)
{
    wxStringTokenizer tkn(Extensions, _T(";, \t"), wxTOKEN_STRTOK);
    wxString wildcard;
    wxArrayString result;
    size_t i;

    while (tkn.HasMoreTokens())
    {
        wildcard = tkn.GetNextToken();
#ifdef __WXMSW__
        wildcard.MakeLower();
#endif
        for (i = 0; i < files.GetCount(); i++)
#ifdef __WXMSW__
            if (files[i].Lower().Matches(wildcard))
#else
            if (files[i].Matches(wildcard))
#endif
            {
                result.Add(files[i]);
            }
    }

    return result;
}



wxString Parser::GetCommand(const wxArrayString& files, 
                            const wxArrayString& keywords, 
                            const wxString& output,
                            const wxString& charset)
{
    wxString cmdline, kline, fline;
    
    cmdline = Command;
    cmdline.Replace(_T("%o"), _T("\"") + output + _T("\""));
    
    wxString dummy;
    size_t i;

    for (i = 0; i < keywords.GetCount(); i++)
    {
        dummy = KeywordItem;
        dummy.Replace(_T("%k"), keywords[i]);
        kline << _T(" ") << dummy;
    }

    for (i = 0; i < files.GetCount(); i++)
    {
        dummy = FileItem;
        dummy.Replace(_T("%f"), _T("\"") + files[i] + _T("\""));
        fline << _T(" ") << dummy;
    }

    wxString charsetline;
    if (!charset.empty())
    {
        charsetline = CharsetItem;
        charsetline.Replace(_T("%c"), charset);
    }

    cmdline.Replace(_T("%C"), charsetline);
    cmdline.Replace(_T("%K"), kline);
    cmdline.Replace(_T("%F"), fline);

    return cmdline;
}
